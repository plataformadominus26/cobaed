/* ==========================================================================
   Control de Asistencia Docente - COBAED
   Lógica de la app móvil (PWA offline-first).

   Reglas que sostienen todo el archivo:
   - El plantel opera en UTC-6. El teléfono puede estar en cualquier zona, así
     que la hora "de la escuela" se calcula siempre con horaPlantel(), nunca
     con getHours() directo.
   - horarios.dia usa el día ISO (1=lunes ... 7=domingo), igual que date('N')
     de PHP. getDay() de JS es domingo=0, por eso se convierte explícitamente.
   - Con red, manda el servidor (this.php). Sin red, se resuelve contra la
     copia local y el registro queda pendiente de sincronizar.
   ========================================================================== */

(() => {
"use strict";

const API          = "this.php";
const LS_TOKEN     = "cobaed_token";
const LS_NOMBRE    = "cobaed_nombre";
const LS_OFFLINE   = "cobaed_offline";

/** SHA-256 de usuario+contraseña, para validar el acceso sin red sin guardar la contraseña. */
async function huellaCredenciales(u, p) {
    const datos = new TextEncoder().encode("cobaed-offline\n" + u.toLowerCase() + "\n" + p);
    const hash = await crypto.subtle.digest("SHA-256", datos);
    return Array.from(new Uint8Array(hash)).map((b) => b.toString(16).padStart(2, "0")).join("");
}
const TZ_PLANTEL   = -6 * 60;   // minutos respecto a UTC
const RESCAN_MS    = 2500;      // pausa tras un escaneo para no repetirlo

const ESTADOS = {
    1: { nombre: "Falta",      clase: "falta",   icono: "bi-x-circle-fill" },
    2: { nombre: "Retraso",    clase: "retraso", icono: "bi-clock-history" },
    3: { nombre: "Asistencia", clase: "asiste",  icono: "bi-check-circle-fill" },
};

const $ = (id) => document.getElementById(id);

// Estado en memoria de la clase resuelta por el último escaneo.
let claseActual = null;
let escaner     = null;
let ocupado     = false;
let ultimoQr    = { texto: "", ts: 0 };

// ---------------------------------------------------------------------------
// Tiempo del plantel (UTC-6), independiente del reloj/zona del teléfono
// ---------------------------------------------------------------------------

/** Devuelve un Date desplazado para que sus getters UTC den la hora local. */
function ahoraPlantel() {
    const d = new Date();
    return new Date(d.getTime() + (TZ_PLANTEL - -d.getTimezoneOffset()) * 60000);
}
const dosDig = (n) => String(n).padStart(2, "0");

/** "HH:MM:SS" en hora del plantel. */
function horaPlantel(d = ahoraPlantel()) {
    return `${dosDig(d.getHours())}:${dosDig(d.getMinutes())}:${dosDig(d.getSeconds())}`;
}
/** "YYYY-MM-DD" en fecha del plantel. */
function fechaPlantel(d = ahoraPlantel()) {
    return `${d.getFullYear()}-${dosDig(d.getMonth() + 1)}-${dosDig(d.getDate())}`;
}
/** "YYYY-MM-DD HH:MM:SS" para columnas DATETIME. */
function selloPlantel() {
    const d = ahoraPlantel();
    return `${fechaPlantel(d)} ${horaPlantel(d)}`;
}
/** Día ISO 1=lunes..7=domingo (horarios.dia). getDay() da domingo=0. */
function diaIsoPlantel() {
    const js = ahoraPlantel().getDay();
    return js === 0 ? 7 : js;
}
/** Compara "HH:MM[:SS]" como texto; normaliza a 8 caracteres. */
const hhmmss = (t) => (String(t || "").length === 5 ? `${t}:00` : String(t || ""));
const hhmm   = (t) => String(t || "").slice(0, 5);

// ---------------------------------------------------------------------------
// Utilidades de interfaz
// ---------------------------------------------------------------------------
const escapar = (t) => { const d = document.createElement("div"); d.textContent = t ?? ""; return d.innerHTML; };

let toastTimer = null;
function toast(msg, ms = 2600) {
    const el = $("toast");
    el.innerHTML = msg;
    el.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { el.hidden = true; }, ms);
}

function vibrar(patron) {
    if (navigator.vibrate) { try { navigator.vibrate(patron); } catch (_) {} }
}

function mostrarVista(id) {
    document.querySelectorAll(".cbd-vista").forEach((v) => v.classList.toggle("activa", v.id === id));
    document.querySelectorAll(".cbd-nav button").forEach((b) => b.classList.toggle("activo", b.dataset.vista === id));
    // La cámara sólo vive mientras la vista de escaneo está visible.
    if (id !== "vistaScan") detenerEscaner();
    if (id === "vistaHist")   pintarHistorial();
    if (id === "vistaCuenta") pintarCuenta();
}

function pintarRed() {
    const i = $("hdrRed");
    if (!i) return;
    i.className = navigator.onLine
        ? "bi bi-wifi cbd-header__net"
        : "bi bi-wifi-off cbd-header__net off";
    i.title = navigator.onLine ? "En línea" : "Sin conexión - los registros se guardan en el teléfono";
}

// ---------------------------------------------------------------------------
// Llamadas al servidor
// ---------------------------------------------------------------------------
async function api(accion, datos = {}, ms = 12000) {
    const cuerpo = new URLSearchParams();
    cuerpo.set("accion", accion);
    cuerpo.set("tkn", localStorage.getItem(LS_TOKEN) || "");
    for (const [k, v] of Object.entries(datos)) cuerpo.set(k, v);

    const ctrl = new AbortController();
    const t = setTimeout(() => ctrl.abort(), ms);
    try {
        const res = await fetch(API, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
            body: cuerpo,
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error("HTTP " + res.status);
        return await res.json();
    } finally {
        clearTimeout(t);
    }
}

/** syncBatch necesita arreglos anidados: se arma el body a mano. */
async function apiLote(records) {
    const cuerpo = new URLSearchParams();
    cuerpo.set("accion", "syncBatch");
    cuerpo.set("tkn", localStorage.getItem(LS_TOKEN) || "");
    records.forEach((r, i) => {
        for (const campo of ["token","maestro_id","area_id","fecha","checkin","checkout","estado","rems"]) {
            cuerpo.set(`records[${i}][${campo}]`, r[campo] ?? "");
        }
    });
    const res = await fetch(API, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
        body: cuerpo,
    });
    if (!res.ok) throw new Error("HTTP " + res.status);
    return res.json();
}

const dbLista = () => (window.localDBInitialized || Promise.resolve()).catch(() => null);

// ---------------------------------------------------------------------------
// Resolución del QR sin conexión (espejo de resolverClase() en PHP)
// ---------------------------------------------------------------------------
function tokenDeQr(texto) {
    let qr = String(texto || "").trim();
    const i = qr.indexOf("area=");
    if (i !== -1) qr = qr.slice(i + 5);
    qr = qr.split("&")[0].trim();
    return /^[A-Za-z0-9]{4,20}$/.test(qr) ? qr : null;
}

async function resolverLocal(textoQr) {
    const base = {
        ok: false, msg: "QR no válido", estado_qr: "invalido",
        area: "", area_id: 0, maestro: "", maestro_id: 0, checkin: "", checkout: "",
    };
    const token = tokenDeQr(textoQr);
    if (!token) return { ...base, msg: "Este código no es un QR de salón COBAED" };

    try {
        const areas = await localDB.query("areas", { token });
        if (!areas.length) return { ...base, estado_qr: "desconocida", msg: "Salón no encontrado en los datos descargados" };

        const area = areas[0];
        const res  = { ...base, area: area.nombre, area_id: Number(area.area_id) };

        const hora = horaPlantel();
        const dia  = diaIsoPlantel();
        const horarios = await localDB.query("horarios", { area_id: Number(area.area_id) });
        const clase = horarios.find((h) =>
            Number(h.dia) === dia && hora >= hhmmss(h.hora) && hora < hhmmss(h.hora1)
        );
        if (!clase) return { ...res, estado_qr: "sin_clase", msg: "No hay clase programada en este salón a esta hora" };

        const mtros = await localDB.query("usuarios", { usuario_id: Number(clase.usuario_id) });
        res.maestro    = mtros.length ? mtros[0].nombre : "Docente no identificado";
        res.maestro_id = Number(clase.usuario_id);
        res.checkin    = hhmmss(clase.hora);
        res.checkout   = hhmmss(clase.hora1);

        // ¿Ya registrada hoy esta clase? (mismo maestro, salón y bloque)
        const hoy = fechaPlantel();
        const previos = await localDB.query("attendance", { maestro_id: res.maestro_id, area_id: res.area_id });
        const dup = previos.some((r) =>
            String(r.fecha || "").slice(0, 10) === hoy && hhmmss(r.checkin) === res.checkin
        );
        if (dup) return { ...res, estado_qr: "duplicado", msg: "Esta clase ya fue registrada" };

        res.ok = true;
        res.estado_qr = "listo";
        res.msg = "Selecciona el estado del docente";
        return res;
    } catch (e) {
        console.error("resolverLocal:", e);
        return { ...base, msg: "No hay datos descargados. Conéctate y sincroniza." };
    }
}

/** Con red pregunta al servidor; sin red (o si falla) usa la copia local. */
async function resolverQr(textoQr) {
    if (navigator.onLine) {
        try {
            const r = await api("resolverQr", { qr: textoQr });
            if (r && r.auth === false) { cerrarSesion(true); return null; }
            if (r) { r.origen = "servidor"; r.qr_texto = textoQr; return r; }
        } catch (e) {
            console.warn("resolverQr en línea falló, uso local:", e.message);
        }
    }
    const r = await resolverLocal(textoQr);
    r.origen = "local";
    r.qr_texto = textoQr;
    return r;
}

// ---------------------------------------------------------------------------
// Escáner
// ---------------------------------------------------------------------------
async function iniciarEscaner() {
    if (escaner) return;
    try {
        escaner = new QrScanner($("video"), (res) => alEscanear(res?.data ?? res), {
            preferredCamera: "environment",
            highlightScanRegion: false,
            highlightCodeOutline: false,
            maxScansPerSecond: 4,
            returnDetailedScanResult: true,
        });
        await escaner.start();
        $("scanApagado").hidden = true;
        $("scanMarco").hidden   = false;
        $("scanAyuda").hidden   = false;
    } catch (e) {
        console.error("cámara:", e);
        escaner = null;
        $("scanApagado").hidden = false;
        $("scanApagado").querySelector("p").textContent =
            "No se pudo abrir la cámara. Revisa los permisos del navegador.";
    }
}

/**
 * Pausa o reanuda la lectura sin apagar la cámara, para no volver a pedir
 * permisos ni sufrir el parpadeo de reiniciar el video.
 */
function pausarEscaner(pausar) {
    if (!escaner) return;
    // start()/pause() devuelven promesa: hay que atrapar el rechazo aparte.
    try {
        const r = pausar ? escaner.pause() : escaner.start();
        if (r && typeof r.catch === "function") r.catch(() => {});
    } catch (_) {}
}

function detenerEscaner() {
    // El panel no debe sobrevivir al cambio de vista.
    if (!$("tarjetaClase").hidden) {
        claseActual = null;
        $("tarjetaClase").hidden = true;
        $("panelFondo").hidden   = true;
        $("tarjetaVacia").hidden = false;
        ultimoQr = { texto: "", ts: 0 };
    }
    if (!escaner) return;
    try { escaner.stop(); escaner.destroy(); } catch (_) {}
    escaner = null;
    $("scanMarco").hidden   = true;
    $("scanAyuda").hidden   = true;
    $("scanApagado").hidden = false;
}

async function alEscanear(texto) {
    if (!texto || ocupado) return;
    // Evita procesar el mismo QR muchas veces por segundo.
    const ahora = Date.now();
    if (texto === ultimoQr.texto && ahora - ultimoQr.ts < RESCAN_MS) return;
    ultimoQr = { texto, ts: ahora };

    ocupado = true;
    vibrar(60);
    try {
        const r = await resolverQr(texto);
        if (r) pintarClase(r);
    } catch (e) {
        console.error(e);
        toast("Error al leer el QR");
    } finally {
        ocupado = false;
    }
}

// ---------------------------------------------------------------------------
// Tarjeta de clase
// ---------------------------------------------------------------------------
function pintarClase(r) {
    claseActual = r;
    $("tarjetaVacia").hidden = true;
    $("panelFondo").hidden   = false;
    $("tarjetaClase").hidden = false;
    // El panel tapa la cámara: no tiene caso seguir escaneando detrás.
    pausarEscaner(true);

    $("clSalon").textContent   = r.area || "Salón no identificado";
    $("clMaestro").textContent = r.maestro || "—";
    $("clBloque").textContent  = r.checkin ? `${hhmm(r.checkin)} - ${hhmm(r.checkout)}` : "Sin horario";
    $("clComentario").value    = "";

    const aviso = $("clAviso");
    const puede = !!r.ok;

    if (puede) {
        aviso.hidden = true;
    } else {
        aviso.hidden = false;
        $("clAvisoTxt").textContent = r.msg || "No se puede registrar";
        aviso.className = "cbd-aviso " + (
            r.estado_qr === "duplicado" ? "cbd-aviso--warn" :
            r.estado_qr === "sin_clase" ? "cbd-aviso--info" : "cbd-aviso--err"
        );
    }

    $("clAcciones").hidden      = !puede;
    $("clComentarioBox").hidden = !puede;
    document.querySelectorAll("#clAcciones .cbd-btn").forEach((b) => { b.disabled = !puede; });

    if (!puede) vibrar([40, 60, 40]);
}

/** Cierra el panel y reanuda el escaneo. */
function limpiarTarjeta() {
    claseActual = null;
    $("tarjetaClase").hidden = true;
    $("panelFondo").hidden   = true;
    $("tarjetaVacia").hidden = false;
    // Permite volver a leer el mismo QR enseguida tras cerrar a mano.
    ultimoQr = { texto: "", ts: 0 };
    pausarEscaner(false);
}

// ---------------------------------------------------------------------------
// Registro de asistencia
// ---------------------------------------------------------------------------
function nuevoToken() {
    const a = new Uint8Array(8);
    crypto.getRandomValues(a);
    return Array.from(a, (b) => b.toString(16).padStart(2, "0")).join("");
}

async function registrar(estado) {
    if (!claseActual || !claseActual.ok || ocupado) return;
    ocupado = true;
    document.querySelectorAll("#clAcciones .cbd-btn").forEach((b) => { b.disabled = true; });

    const c    = claseActual;
    const rems = $("clComentario").value.trim().slice(0, 200);
    const reg  = {
        token: nuevoToken(),
        maestro_id: c.maestro_id,
        area_id: c.area_id,
        fecha: selloPlantel(),
        checkin: c.checkin,
        checkout: c.checkout,
        estado: estado,
        rems: rems,
        usuario_id: Number(localStorage.getItem("cobaed_uid") || 0),
        sync_status: "pending",
    };

    let enviado = false;
    if (navigator.onLine) {
        try {
            const r = await api("registrar", { qr: c.qr_texto || "", estado, rems });
            enviado = !!(r && r.ok);
            if (r && !r.ok && r.estado_qr === "duplicado") {
                toast("Esta clase ya estaba registrada");
            }
        } catch (e) {
            console.warn("registro en línea falló, guardo local:", e.message);
        }
    }

    // Siempre queda copia local: es la fuente del historial y del pendiente.
    try {
        await localDB.push("attendance", { ...reg, sync_status: enviado ? "synced" : "pending" });
    } catch (e) {
        console.error("no se pudo guardar local:", e);
    }

    vibrar(estado === 3 ? 90 : [70, 50, 70]);
    flash(estado, c.maestro, enviado);
    limpiarTarjeta();
    ocupado = false;
    actualizarPendientes();
}

function flash(estado, maestro, enviado) {
    const meta = ESTADOS[estado] || ESTADOS[3];
    const el = $("flash");
    el.className = "cbd-flash cbd-flash--" + meta.clase;
    $("flashIcono").className = "bi " + meta.icono;
    $("flashTitulo").textContent = meta.nombre + " registrada";
    $("flashSub").textContent = (maestro || "") + (enviado ? "" : " · guardado sin conexión");
    el.hidden = false;
    setTimeout(() => { el.hidden = true; }, 1500);
}

// ---------------------------------------------------------------------------
// Historial
// ---------------------------------------------------------------------------
async function pintarHistorial() {
    const cont = $("listaHist");
    let regs = [];
    try {
        regs = await localDB.query("attendance", {});
    } catch (_) {}

    const hoy = fechaPlantel();
    const deHoy = regs
        .filter((r) => String(r.fecha || "").slice(0, 10) === hoy)
        .sort((a, b) => String(b.fecha).localeCompare(String(a.fecha)));

    const cuenta = { 1: 0, 2: 0, 3: 0 };
    deHoy.forEach((r) => { cuenta[Number(r.estado)] = (cuenta[Number(r.estado)] || 0) + 1; });
    $("sumAsiste").textContent  = cuenta[3] || 0;
    $("sumRetraso").textContent = cuenta[2] || 0;
    $("sumFalta").textContent   = cuenta[1] || 0;

    if (!deHoy.length) {
        cont.innerHTML = `<div class="cbd-vacio"><i class="bi bi-clipboard-x"></i>
            Aún no hay registros hoy</div>`;
        return;
    }

    // Nombres de maestro y salón desde la copia local.
    const [mtros, areas] = await Promise.all([
        localDB.query("usuarios", {}).catch(() => []),
        localDB.query("areas", {}).catch(() => []),
    ]);
    const nomMtro = new Map(mtros.map((u) => [Number(u.usuario_id), u.nombre]));
    const nomArea = new Map(areas.map((a) => [Number(a.area_id), a.nombre]));

    cont.innerHTML = deHoy.map((r) => {
        const meta = ESTADOS[Number(r.estado)] || { nombre: "—", clase: "" };
        const mtro = r.maestro || nomMtro.get(Number(r.maestro_id)) || "Docente";
        const area = r.area    || nomArea.get(Number(r.area_id))    || "Salón";
        return `<div class="cbd-reg cbd-reg--${meta.clase}">
            <div class="cbd-reg__top">
                <span class="cbd-reg__mtro">${escapar(mtro)}</span>
                <span class="cbd-reg__hora">${escapar(hhmm(r.checkin))}</span>
            </div>
            <div class="cbd-reg__meta">${escapar(area)} · ${escapar(meta.nombre)}</div>
            ${r.rems ? `<div class="cbd-reg__rems">${escapar(r.rems)}</div>` : ""}
            ${r.sync_status === "pending" ? `<div class="cbd-reg__pend"><i class="bi bi-cloud-arrow-up"></i> pendiente de enviar</div>` : ""}
        </div>`;
    }).join("");
}

// ---------------------------------------------------------------------------
// Cuenta y sincronización
// ---------------------------------------------------------------------------
async function contarPendientes() {
    try { return (await localDB.query("attendance", { sync_status: "pending" })).length; }
    catch (_) { return 0; }
}

async function actualizarPendientes() {
    const n = await contarPendientes();
    const badge = $("navBadge");
    badge.hidden = n === 0;
    badge.textContent = n;
    const p = $("ctaPend");
    if (p) p.textContent = n;
}

async function pintarCuenta() {
    $("ctaNombre").textContent = localStorage.getItem(LS_NOMBRE) || "—";
    $("ctaDatos").textContent  = `${fechaPlantel()} · ${hhmm(horaPlantel())} (hora del plantel)`;
    $("ctaVersion").textContent = navigator.onLine ? "Conectado" : "Sin conexión";
    actualizarPendientes();
}

/**
 * Reemplaza el contenido de un almacén local.
 * push() de EasyDB nunca resuelve si recibe un arreglo vacío (su contador
 * interno jamás llega al total), así que el caso vacío se corta aquí.
 */
async function reemplazar(almacen, filas) {
    await localDB.zap(almacen);
    if (Array.isArray(filas) && filas.length) await localDB.push(almacen, filas);
}

/**
 * Sube los pendientes y vuelve a descargar el catálogo.
 * Sólo marca como sincronizados los tokens que el servidor confirmó.
 */
async function sincronizar(silencioso = false) {
    if (!navigator.onLine) { if (!silencioso) toast("Sin conexión"); return; }

    const btn = $("btnSync");
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="cbd-spin"></span> Sincronizando...'; }

    try {
        // 1. Subir pendientes en lotes.
        const pend = await localDB.query("attendance", { sync_status: "pending" });
        let enviados = 0;
        for (let i = 0; i < pend.length; i += 50) {
            const lote = pend.slice(i, i + 50);
            const r = await apiLote(lote);
            if (!r || !r.ok) throw new Error(r && r.msg ? r.msg : "Fallo al enviar");

            // El servidor devuelve los tokens aceptados; se marcan por _ai_id
            // (update() de EasyDB usa la llave interna, no el token).
            const aceptados = new Set(r.tokens || []);
            for (const reg of lote) {
                if (aceptados.has(reg.token) && reg._ai_id !== undefined) {
                    await localDB.update("attendance", reg._ai_id, { sync_status: "synced" });
                    enviados++;
                }
            }
        }

        // 2. Descargar catálogo fresco (docentes, salones, horarios).
        const boot = await api("bootstrap", {}, 30000);
        if (boot && boot.auth === false) { cerrarSesion(true); return; }
        if (boot && boot.ok) {
            await reemplazar("usuarios", boot.usuarios);
            await reemplazar("areas",    boot.areas);
            await reemplazar("horarios", boot.horarios);
            await reemplazar("info",     boot.info);

            // El historial del servidor reemplaza sólo lo ya sincronizado;
            // los pendientes locales se conservan.
            const locales = await localDB.query("attendance", {});
            const quedan  = locales.filter((r) => r.sync_status === "pending");
            await reemplazar("attendance", [
                ...(boot.attendance || []),
                ...quedan.map(({ _ai_id, ...r }) => r),
            ]);

            if (boot.info && boot.info[0]) {
                localStorage.setItem("cobaed_uid", boot.info[0].usuario_id);
            }
        }

        if (!silencioso) toast(enviados ? `Listo · ${enviados} registro(s) enviados` : "Datos actualizados");
    } catch (e) {
        console.error("sincronizar:", e);
        if (!silencioso) toast("No se pudo sincronizar");
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Sincronizar ahora'; }
        actualizarPendientes();
    }
}

// ---------------------------------------------------------------------------
// Sesión
// ---------------------------------------------------------------------------
function cerrarSesion(invalida) {
    detenerEscaner();
    if (invalida === true) localStorage.removeItem(LS_OFFLINE);
    localStorage.removeItem(LS_TOKEN);
    localStorage.removeItem(LS_NOMBRE);
    localStorage.removeItem("cobaed_uid");
    $("pantallaApp").hidden   = true;
    $("pantallaLogin").hidden = false;
}

function abrirApp() {
    $("pantallaLogin").hidden = true;
    $("pantallaApp").hidden   = false;
    $("hdrUsuario").textContent = localStorage.getItem(LS_NOMBRE) || "";
    pintarRed();
    mostrarVista("vistaScan");
    actualizarPendientes();
}

async function entrar(ev) {
    ev.preventDefault();
    const u = $("usuario").value.trim();
    const p = $("clave").value;
    const err = $("loginError");
    err.hidden = true;

    if (!u || !p) { err.textContent = "Escribe usuario y contraseña"; err.hidden = false; return; }

    const btn = $("btnEntrar");
    btn.disabled = true; btn.innerHTML = '<span class="cbd-spin"></span> Entrando...';

    try {
        if (navigator.onLine) {
            const r = await api("login", { u, p });
            if (!r || !r.ok) throw new Error(r && r.msg ? r.msg : "Credenciales incorrectas");
            localStorage.setItem(LS_TOKEN, r.token);
            localStorage.setItem(LS_NOMBRE, r.nombre || "");
            // Huella de TUS credenciales para poder entrar sin red más adelante (nunca la contraseña).
            try {
                localStorage.setItem(LS_OFFLINE, JSON.stringify({
                    h: await huellaCredenciales(u, p), token: r.token, nombre: r.nombre || ""
                }));
            } catch (e) { /* sin crypto.subtle: no habrá acceso sin red */ }
            abrirApp();
            sincronizar(true);
        } else {
            // Sin red: solo entra quien ya inició sesión antes con internet en este equipo.
            const guardado = JSON.parse(localStorage.getItem(LS_OFFLINE) || "null");
            if (!guardado || guardado.h !== await huellaCredenciales(u, p)) {
                throw new Error("Sin conexión: entra una vez con internet en este equipo");
            }
            localStorage.setItem(LS_TOKEN, guardado.token);
            localStorage.setItem(LS_NOMBRE, guardado.nombre || "");
            abrirApp();
        }
    } catch (e) {
        err.textContent = e.message || "No se pudo iniciar sesión";
        err.hidden = false;
    } finally {
        btn.disabled = false; btn.textContent = "Entrar";
    }
}

// ---------------------------------------------------------------------------
// Arranque
// ---------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", async () => {
    await dbLista();

    $("formLogin").addEventListener("submit", entrar);
    $("btnActivarCam").addEventListener("click", iniciarEscaner);
    $("btnCerrarPanel").addEventListener("click", limpiarTarjeta);
    $("panelFondo").addEventListener("click", limpiarTarjeta);
    // Escape cierra el panel (útil al probar desde escritorio).
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !$("tarjetaClase").hidden) limpiarTarjeta();
    });
    $("btnSync").addEventListener("click", () => sincronizar(false));
    $("btnSalir").addEventListener("click", cerrarSesion);
    $("btnActualizar").addEventListener("click", actualizarApp);

    document.querySelectorAll(".cbd-nav button").forEach((b) => {
        b.addEventListener("click", () => mostrarVista(b.dataset.vista));
    });
    document.querySelectorAll("#clAcciones .cbd-btn").forEach((b) => {
        b.addEventListener("click", () => registrar(Number(b.dataset.estado)));
    });

    window.addEventListener("online",  () => { pintarRed(); sincronizar(true); });
    window.addEventListener("offline", pintarRed);
    // Al volver a la app, reintenta enviar lo pendiente.
    document.addEventListener("visibilitychange", () => {
        if (!document.hidden && navigator.onLine) sincronizar(true);
    });

    if (localStorage.getItem(LS_TOKEN)) {
        abrirApp();
        sincronizar(true);
    } else {
        $("pantallaLogin").hidden = false;
    }

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("service-worker.js").catch(() => {});
    }
});

function actualizarApp() {
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.getRegistration().then((reg) => {
            if (reg) { reg.update(); if (reg.waiting) reg.waiting.postMessage({ type: "SKIP_WAITING" }); }
        });
    }
    toast("Buscando actualización...");
    setTimeout(() => location.reload(), 1200);
}

})();
