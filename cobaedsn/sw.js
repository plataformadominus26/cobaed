const CACHE_NAME = 'app-v1';

// LISTA ACTUALIZADA SEGÚN TU ESTRUCTURA REAL
const STATIC_ASSETS = [
  './',
  './index.html',
  './biblioteca.html',
  './manifest.json',
  // CSS
  './assets/css/bootstrap.min.css',
  './assets/css/bootstrap-icons.css',
  './assets/css/this.css',
  // Fuentes (Vitales para que los iconos no se rompan offline)
  './assets/css/fonts/bootstrap-icons.woff',
  './assets/css/fonts/bootstrap-icons.woff2',
  // JS
  './assets/js/jquery-3.7.1.min.js',
  './assets/js/bootstrap.bundle.min.js',
  './assets/js/qr-scanner.umd.min.js',
  './assets/js/this.js'
];

// 1. Instalación: Cacheamos lo mínimo vital
self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting(); // Fuerza al SW a activarse inmediatamente
});

// 2. Activación: Limpiamos cachés viejas (útil cuando cambies a 'app-v2')
self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  return self.clients.claim();
});

// 3. Fetch: ESTRATEGIA NETWORK FIRST (Red primero, luego caché)
self.addEventListener('fetch', (e) => {
  // Solo interceptamos peticiones GET y que sean de nuestra propia app (no APIs externas por ahora)
  if (e.request.method !== 'GET' || !e.request.url.startsWith(self.location.origin)) {
    return;
  }

  e.respondWith(
    fetch(e.request)
      .then((networkResponse) => {
        // Si hay red y responde bien, actualizamos la caché y entregamos el archivo
        return caches.open(CACHE_NAME).then((cache) => {
          cache.put(e.request, networkResponse.clone());
          return networkResponse;
        });
      })
      .catch(() => {
        // Si falla la red (offline), buscamos en caché
        return caches.match(e.request);
      })
  );
});