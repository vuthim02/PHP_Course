<?php

declare(strict_types=1);

namespace UrlShortener\Middleware;

use UrlShortener\Cache\CacheInterface;

/**
 * HTTP cache middleware implementing cache-aside pattern.
 * Sets appropriate Cache-Control headers for CDN-ready responses.
 */
final class CacheMiddleware
{
    private const int REDIRECT_CACHE_TTL = 3600; // 1 hour for redirects

    public function __construct(
        private readonly CacheInterface $cache
    ) {}

    /**
     * Get cached response headers for a short code redirect.
     */
    public function getRedirectHeaders(string $shortCode): array
    {
        $cached = $this->cache->get("redirect:headers:{$shortCode}");
        if ($cached !== null) {
            return $cached;
        }

        $headers = [
            'Cache-Control' => 'public, max-age=' . self::REDIRECT_CACHE_TTL . ', s-maxage=' . (self::REDIRECT_CACHE_TTL * 2),
            'Pragma' => 'cache',
            'Expires' => gmdate('D, d M Y H:i:s', time() + self::REDIRECT_CACHE_TTL) . ' GMT',
        ];

        $this->cache->set("redirect:headers:{$shortCode}", $headers, self::REDIRECT_CACHE_TTL);

        return $headers;
    }

    /**
     * CDN edge rules configuration for reverse proxy caching.
     */
    public static function getCdnRules(): array
    {
        return [
            'cache_ttl' => self::REDIRECT_CACHE_TTL * 2,
            'stale_while_revalidate' => 86400,
            'stale_if_error' => 604800,
        ];
    }
}
