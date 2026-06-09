// Service worker Naaqati — stratégie "réseau d'abord" (network-first).
// Les utilisateurs en ligne ont toujours la version fraîche ; le cache ne
// sert que de secours hors-ligne. On ne touche jamais aux requêtes non-GET
// (Livewire/POST passent toujours par le réseau).
const CACHE = 'naaqati-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    if (req.method !== 'GET' || !req.url.startsWith('http')) {
        return;
    }

    event.respondWith(
        fetch(req)
            .then((res) => {
                const copy = res.clone();
                caches.open(CACHE).then((c) => c.put(req, copy)).catch(() => {});
                return res;
            })
            .catch(() => caches.match(req))
    );
});
