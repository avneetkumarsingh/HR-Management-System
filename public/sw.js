const CACHE_NAME = 'walkwel-pwa-v1';
const ASSETS_TO_CACHE = [
    '/dashboard',
    // We cache minimal elements natively to satisfy generic PWA install protocols for mobile browsers
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then(cache => {
            return cache.addAll(ASSETS_TO_CACHE).catch(err => {
                // Ignore failure on dynamic caching since laravel manages static assets via mix/vite 
                console.log('Minor caching skip during install phase.');
            });
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
