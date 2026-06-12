/**
 * Invexa — Service Worker
 * Estratégia: Network First para páginas, Cache First para assets estáticos
 */

const CACHE_NAME     = 'invexa-v1';
const ASSETS_CACHE   = 'invexa-assets-v1';
const OFFLINE_URL    = '/offline';

// Assets estáticos para cache imediato
const PRECACHE_ASSETS = [
    '/offline',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
];

// ── Install: pré-cache dos assets essenciais ─────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(ASSETS_CACHE)
            .then(cache => cache.addAll(PRECACHE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ── Activate: limpar caches antigos ──────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME && key !== ASSETS_CACHE)
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch: estratégia por tipo de recurso ────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorar: não-GET, extensões externas não mapeadas, admin routes
    if (request.method !== 'GET') return;
    if (url.pathname.startsWith('/telescope')) return;
    if (url.pathname.startsWith('/webhook')) return;

    // Assets estáticos (cdn, imagens, fonts) → Cache First
    if (
        url.origin !== self.location.origin ||
        url.pathname.match(/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|webp|ico)$/)
    ) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Páginas da aplicação → Network First com fallback offline
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirstWithOfflineFallback(request));
        return;
    }

    // APIs / JSON → Network First sem fallback
    event.respondWith(networkFirst(request));
});

// ── Estratégias ───────────────────────────────────────────────────────────────
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(ASSETS_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 408 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response(JSON.stringify({ error: 'offline' }), {
            headers: { 'Content-Type': 'application/json' },
            status: 503,
        });
    }
}

async function networkFirstWithOfflineFallback(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        const offline = await caches.match(OFFLINE_URL);
        return offline || new Response('<h1>Sem conexão</h1>', {
            headers: { 'Content-Type': 'text/html' },
            status: 503,
        });
    }
}
