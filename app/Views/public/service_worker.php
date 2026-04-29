const CACHE_PREFIX = <?= json_encode($cachePrefix, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();

    await Promise.all(keys.map((key) => {
      if (!key.startsWith(CACHE_PREFIX)) {
        return Promise.resolve(false);
      }

      return caches.delete(key);
    }));

    await self.clients.claim();
  })());
});
