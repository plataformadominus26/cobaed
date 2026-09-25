<?php
// ============================================================================
// API de la app móvil - Control de Asistencia Docente (COBAED)
//
// Acciones (POST):
//   login        u, p                      -> autentica supervisor, devuelve token
//   resolverQr   tkn, qr                   -> resuelve salón + clase vigente
//   registrar    tkn, qr, estado, rems     -> guarda asistencia (online)
//   syncBatch    tkn, records[]            -> sube registros creados offline
//   bootstrap    tkn                       -> catálogo para operar sin red
//   historial    tkn                       -> últimos registros del supervisor
//
// Toda hora/fecha se toma del servidor en UTC-6 (ver conexion1.php). El cliente
// nunca decide qué clase corresponde: sólo escanea y elige el estado.
// ============================================================================

require_once "conexion1.php";

header('Content-Type: application/json; charset=utf-8');

define("QR_PREFIX", "https://" . ($_SERVER["HTTP_HOST"] ?? "cobaedlomas.com") . "/cobaed/newsboard.php?area=");
const EST_FALTA   = 1;
const EST_RETRASO = 2;
const EST_ASISTE  = 3;

function responder(array $r) { echo json_encode($r, JSON_UNESCAPED_UNICODE); exit; }
function fallar(string $msg, array $extra = []) { responder(["ok" => false, "msg" => $msg] + $extra); }

/** Extrae el token de área de un QR; acepta la URL completa o el token pelón. */
function tokenDeQr(string $qr): ?string {
    $qr = trim($qr);
    if ($qr === '') return null;
    if (($pos = strpos($qr, "area=")) !== false) {
        $qr = substr($qr, $pos + 5);
    }
    $qr = trim(explode('&', $qr)[0]);
    return preg_match('/^[A-Za-z0-9]{4,20}$/', $qr) ? $qr : null;
}

/** Valida el token de sesión y regresa el supervisor dueño de la sesión. */
function supervisorPorToken(mysqli $db, ?string $token): ?array {
    if (!$token) return null;
    $st = $db->prepare("SELECT usuario_id, book_id, nombre, token FROM usuarios WHERE token=? AND activo=1 LIMIT 1");
    $st->bind_param("s", $token);
    $st->execute();
    return $st->get_result()->fetch_assoc() ?: null;
}

/**
 * Resuelve el salón y la clase que corresponde en este momento.
 * Devuelve ["estado"=>..., ...]; "estado" describe por qué no se puede registrar.
 */
function resolverClase(mysqli $db, array $user, string $qrRaw): array {
    $out = [
        "ok" => false, "msg" => "QR no válido", "estado_qr" => "invalido",
        "area" => "", "area_id" => 0, "maestro" => "", "maestro_id" => 0,
        "checkin" => "", "checkout" => "", "dia" => diaLocal(), "hora" => horaLocal(),
    ];

    $areaToken = tokenDeQr($qrRaw);
    if (!$areaToken) {
        $out["msg"] = "Este código no es un QR de salón COBAED";
        return $out;
    }

    // El área debe pertenecer al mismo plantel (book_id) que el supervisor.
    $st = $db->prepare("SELECT area_id, nombre, book_id FROM areas WHERE token=? AND activa=1 LIMIT 1");
    $st->bind_param("s", $areaToken);
    $st->execute();
    $area = $st->get_result()->fetch_assoc();

    if (!$area) {
        $out["estado_qr"] = "desconocida";
        $out["msg"] = "Salón no encontrado";
        return $out;
    }
    if ((int)$area["book_id"] !== (int)$user["book_id"]) {
        $out["estado_qr"] = "ajena";
        $out["msg"] = "Este salón no pertenece a tu plantel";
        return $out;
    }

    $out["area"]    = $area["nombre"];
    $out["area_id"] = (int)$area["area_id"];

    // Clase vigente: mismo día ISO y la hora local dentro de [hora, hora1).
    $dia  = diaLocal();
    $hora = horaLocal();
    $sql = "SELECT h.horario_id, h.hora, h.hora1, h.usuario_id, u.nombre AS mtro
            FROM horarios h JOIN usuarios u ON u.usuario_id = h.usuario_id
            WHERE h.area_id=? AND h.dia=? AND ? >= h.hora AND ? < h.hora1
            ORDER BY h.hora LIMIT 1";
    $st = $db->prepare($sql);
    $st->bind_param("iiss", $out["area_id"], $dia, $hora, $hora);
    $st->execute();
    $clase = $st->get_result()->fetch_assoc();

    if (!$clase) {
        $out["estado_qr"] = "sin_clase";
        $out["msg"] = "No hay clase programada en este salón a esta hora";
        return $out;
    }

    $out["maestro"]     = $clase["mtro"];
    $out["maestro_id"]  = (int)$clase["usuario_id"];
    $out["checkin"]     = $clase["hora"];
    $out["checkout"]    = $clase["hora1"];
    $out["horario_id"]  = (int)$clase["horario_id"];

    // ¿Ya se registró esta misma clase (maestro+salón+bloque) hoy?
    $hoy = fechaLocal();
    $sql = "SELECT a.attendance_id, a.estado, u.nombre AS quien
            FROM attendance a LEFT JOIN usuarios u ON u.usuario_id = a.usuario_id
            WHERE a.maestro_id=? AND a.area_id=? AND DATE(a.fecha)=? AND a.checkin=? LIMIT 1";
    $st = $db->prepare($sql);
    $st->bind_param("iiss", $out["maestro_id"], $out["area_id"], $hoy, $out["checkin"]);
    $st->execute();
    if ($ya = $st->get_result()->fetch_assoc()) {
        $out["estado_qr"] = "duplicado";
        $out["estado_previo"] = (int)$ya["estado"];
        $out["msg"] = "Esta clase ya fue registrada" . ($ya["quien"] ? " por " . $ya["quien"] : "");
        return $out;
    }

    $out["ok"]  = true;
    $out["estado_qr"] = "listo";
    $out["msg"] = "Selecciona el estado del docente";
    return $out;
}

/** Inserta un registro de asistencia; ignora silenciosamente el duplicado exacto. */
function guardarAsistencia(mysqli $db, array $datos): bool {
    $sql = "INSERT INTO attendance (token, fecha, usuario_id, checkin, checkout, estado, area_id, rems, maestro_id)
            VALUES (?,?,?,?,?,?,?,?,?)";
    $st = $db->prepare($sql);
    $st->bind_param(
        "ssissiisi",
        $datos["token"], $datos["fecha"], $datos["usuario_id"],
        $datos["checkin"], $datos["checkout"], $datos["estado"],
        $datos["area_id"], $datos["rems"], $datos["maestro_id"]
    );
    return $st->execute();
}

/** ¿Existe ya un registro para esta clase/día? Evita duplicados al sincronizar. */
function yaRegistrada(mysqli $db, int $maestro_id, int $area_id, string $fecha, string $checkin): bool {
    $st = $db->prepare("SELECT 1 FROM attendance WHERE maestro_id=? AND area_id=? AND DATE(fecha)=? AND checkin=? LIMIT 1");
    $st->bind_param("iiss", $maestro_id, $area_id, $fecha, $checkin);
    $st->execute();
    return (bool)$st->get_result()->fetch_row();
}

function nuevoToken(): string { return substr(bin2hex(random_bytes(8)), 0, 15); }

// ---------------------------------------------------------------------------
// Ruteo
// ---------------------------------------------------------------------------
$accion = $_REQUEST["accion"] ?? '';
$tkn    = $_REQUEST["tkn"] ?? '';

if ($accion === 'login') {
    $u = $_REQUEST["u"] ?? '';
    $p = $_REQUEST["p"] ?? '';
    $st = $db->prepare("SELECT usuario_id, token, nombre FROM usuarios WHERE email=? AND pwd=? AND activo=1 LIMIT 1");
    $st->bind_param("ss", $u, $p);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if (!$row) fallar("Usuario o contraseña incorrectos");
    responder(["ok" => true, "token" => $row["token"], "nombre" => $row["nombre"]]);
}

// Todo lo demás exige sesión válida.
$user = supervisorPorToken($db, $tkn);
if (!$user) fallar("Sesión no válida. Inicia sesión de nuevo.", ["auth" => false]);

if ($accion === 'resolverQr') {
    responder(resolverClase($db, $user, $_REQUEST["qr"] ?? ''));
}

if ($accion === 'registrar') {
    $r = resolverClase($db, $user, $_REQUEST["qr"] ?? '');
    if (!$r["ok"]) responder($r);

    $estado = (int)($_REQUEST["estado"] ?? 0);
    if (!in_array($estado, [EST_FALTA, EST_RETRASO, EST_ASISTE], true)) {
        fallar("Estado de asistencia no válido");
    }

    $ok = guardarAsistencia($db, [
        "token"      => nuevoToken(),
        "fecha"      => ahoraLocal(),
        "usuario_id" => (int)$user["usuario_id"],
        "checkin"    => $r["checkin"],
        "checkout"   => $r["checkout"],
        "estado"     => $estado,
        "area_id"    => $r["area_id"],
        "rems"       => mb_substr(trim($_REQUEST["rems"] ?? ''), 0, 200),
        "maestro_id" => $r["maestro_id"],
    ]);

    if (!$ok) fallar("No se pudo guardar el registro");
    responder(["ok" => true, "msg" => "Registro guardado", "maestro" => $r["maestro"], "area" => $r["area"]]);
}

if ($accion === 'syncBatch') {
    $records = $_REQUEST["records"] ?? [];
    if (!is_array($records)) fallar("Formato de lote inválido");

    $guardados = 0; $omitidos = 0; $tokens = [];
    foreach ($records as $rec) {
        $maestro_id = (int)($rec["maestro_id"] ?? 0);
        $area_id    = (int)($rec["area_id"] ?? 0);
        $estado     = (int)($rec["estado"] ?? 0);
        $fecha      = $rec["fecha"] ?? '';
        $checkin    = $rec["checkin"] ?? '';
        $checkout   = $rec["checkout"] ?? '';
        $token      = substr((string)($rec["token"] ?? nuevoToken()), 0, 15);

        if (!$maestro_id || !$area_id || !in_array($estado, [EST_FALTA, EST_RETRASO, EST_ASISTE], true)
            || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fecha)
            || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $checkin)) {
            $omitidos++;
            $tokens[] = $token;   // inválido: no reintentar eternamente
            continue;
        }

        if (yaRegistrada($db, $maestro_id, $area_id, substr($fecha, 0, 10), $checkin)) {
            $omitidos++;
            $tokens[] = $token;   // ya estaba en el servidor: darlo por sincronizado
            continue;
        }

        $ok = guardarAsistencia($db, [
            "token" => $token, "fecha" => $fecha, "usuario_id" => (int)$user["usuario_id"],
            "checkin" => $checkin, "checkout" => $checkout ?: $checkin, "estado" => $estado,
            "area_id" => $area_id, "rems" => mb_substr((string)($rec["rems"] ?? ''), 0, 200),
            "maestro_id" => $maestro_id,
        ]);
        if ($ok) { $guardados++; $tokens[] = $token; }
    }

    responder([
        "ok" => true,
        "guardados" => $guardados,
        "omitidos"  => $omitidos,
        "total"     => count($records),
        "tokens"    => $tokens,   // el cliente marca sólo estos como sincronizados
    ]);
}

if ($accion === 'bootstrap') {
    $book_id = (int)$user["book_id"];

    $st = $db->prepare("SELECT usuario_id, token, nombre, email, pwd, book_id FROM usuarios WHERE book_id=? AND activo=1 ORDER BY nombre");
    $st->bind_param("i", $book_id);
    $st->execute();
    $usuarios = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    $st = $db->prepare("SELECT area_id, token, nombre, book_id FROM areas WHERE book_id=? AND activa=1 ORDER BY nombre");
    $st->bind_param("i", $book_id);
    $st->execute();
    $areas = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    $st = $db->prepare("SELECT h.horario_id, h.usuario_id, h.dia, h.hora, h.hora1, h.area_id
                        FROM horarios h JOIN usuarios u ON u.usuario_id=h.usuario_id
                        WHERE u.book_id=? ORDER BY h.dia, h.hora");
    $st->bind_param("i", $book_id);
    $st->execute();
    $horarios = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    $uid = (int)$user["usuario_id"];
    $st = $db->prepare("SELECT token, fecha, checkin, checkout, estado, area_id, rems, maestro_id, usuario_id, 'synced' AS sync_status
                        FROM attendance WHERE usuario_id=? AND fecha >= DATE_SUB(?, INTERVAL 30 DAY)
                        ORDER BY fecha DESC LIMIT 200");
    $hoy = fechaLocal();
    $st->bind_param("is", $uid, $hoy);
    $st->execute();
    $attendance = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    responder([
        "ok" => true,
        "usuarios" => $usuarios, "areas" => $areas, "horarios" => $horarios,
        "attendance" => $attendance,
        "info" => [[
            "id" => 1, "rKey" => "config_{$book_id}",
            "book_id" => $book_id, "fecha" => fechaLocal(), "hora" => horaLocal(),
            "usuario_id" => $uid, "nombre" => $user["nombre"],
        ]],
    ]);
}

if ($accion === 'historial') {
    $uid = (int)$user["usuario_id"];
    $sql = "SELECT a.token, a.fecha, a.checkin, a.checkout, a.estado, a.rems,
                   m.nombre AS maestro, c.nombre AS area
            FROM attendance a
            LEFT JOIN usuarios m ON m.usuario_id = a.maestro_id
            LEFT JOIN areas   c ON c.area_id    = a.area_id
            WHERE a.usuario_id=? ORDER BY a.fecha DESC LIMIT 100";
    $st = $db->prepare($sql);
    $st->bind_param("i", $uid);
    $st->execute();
    responder(["ok" => true, "registros" => $st->get_result()->fetch_all(MYSQLI_ASSOC)]);
}

fallar("Acción no reconocida");
