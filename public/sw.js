/* SwiftPay PWA — Service Worker */
const CACHE_VERSION = 'swiftpay-v1';
const PRECACHE_URLS = [
    '/offline.html',
    '/site/manifest.json',
    '/site/img/icon-192.png',
    '/site/img/icon-512.png',
    '/site/img/favicon-32.png',
    '/site/img/apple-touch-icon.png',
    '/site/img/swift_pay_solucoes_png.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((key) => key !== CACHE_VERSION).map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() =>
                caches.match('/offline.html').then((cached) => cached || Response.error())
            )
        );
        return;
    }

    if (url.pathname.startsWith('/site/')) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                if (cached) {
                    return cached;
                }

                return fetch(event.request).then((response) => {
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    const copy = response.clone();
                    caches.open(CACHE_VERSION).then((cache) => cache.put(event.request, copy));
                    return response;
                });
            })
        );
    }
});
