/* ==========================================================================
   Service worker - Asistencia Docente COBAED

   Estrategia:
   - Rutas relativas: la app vive en /cobaed/movil/, no en la raíz del dominio.
   - El "shell" (HTML, CSS, JS, iconos) se sirve desde caché y se refresca en
     segundo plano.
   - this.php NUNCA se cachea: es la API. Cachearla devolvería horarios y
     asistencias viejas, que es justo lo que no debe pasar.
   ========================================================================== */

const CACHE = "cobaed-asistencia-v5";

const SHELL = [
    "./",
    "./index.html",
    "./movil.css?v=4",
    "./this.js?v=5",
    "./manifest.json",
    "./assets/css/bootstrap-icons.min.css",
    "./assets/css/fonts/bootstrap-icons.woff",
    "./assets/css/fonts/bootstrap-icons.woff2",
    "./assets/js/qr-scanner.umd.min.js",
    "./assets/js/easyDb.js",
    "./assets/img/cobaed2010.png",
    "./assets/img/icons/favico.png",
    "./assets/img/icons/icon-192x192.png",
    "./assets/img/icons/icon-512x512.png",
];

self.addEventListener("install", (e) => {
    e.waitUntil(
        caches.open(CACHE)
            // Se cachea de a uno: un 404 suelto no debe abortar la instalación.
            .then((c) => Promise.all(SHELL.map((u) => c.add(u).catch(() => null))))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener("activate", (e) => {
    e.waitUntil(
        caches.keys()
            .then((ks) => Promise.all(ks.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", (e) => {
    const req = e.request;
    if (req.method !== "GET") return;                 // POST a la API: pasa directo

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;  // recursos externos: sin tocar
    if (url.pathname.endsWith("this.php")) return;    // API: siempre a la red

    // Navegación: red primero para tomar la versión nueva, caché si no hay red.
    if (req.mode === "navigate") {
        e.respondWith(
            fetch(req)
                .then((r) => {
                    const copia = r.clone();
                    caches.open(CACHE).then((c) => c.put("./index.html", copia));
                    return r;
                })
                .catch(() => caches.match("./index.html"))
        );
        return;
    }

    // Estáticos: caché primero, con refresco silencioso en segundo plano.
    e.respondWith(
        caches.match(req).then((hit) => {
            const red = fetch(req)
                .then((r) => {
                    if (r && r.status === 200 && r.type === "basic") {
                        const copia = r.clone();
                        caches.open(CACHE).then((c) => c.put(req, copia));
                    }
                    return r;
                })
                .catch(() => hit);
            return hit || red;
        })
    );
});

self.addEventListener("message", (e) => {
    if (e.data && e.data.type === "SKIP_WAITING") self.skipWaiting();
});
