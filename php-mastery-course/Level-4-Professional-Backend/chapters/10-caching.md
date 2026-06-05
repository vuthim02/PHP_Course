# Chapter 10: Caching Strategies

## Learning Objectives

- Implement response caching
- Use ETags and cache headers
- Set up Redis caching layer
- Apply CDN caching

---

## 10.1 Response Caching

```php
<?php
class ResponseCache
{
    public function __construct(private CacheInterface $cache) {}

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        if ($cached = $this->cache->get($key)) {
            return $cached;
        }

        $value = $callback();
        $this->cache->set($key, $value, $ttl);
        return $value;
    }

    public function cacheResponse(string $uri, int $ttl = 300): void
    {
        if ($cached = $this->cache->get("response:{$uri}")) {
            header('X-Cache: HIT');
            echo $cached;
            exit;
        }

        ob_start(function ($buffer) use ($uri, $ttl) {
            $this->cache->set("response:{$uri}", $buffer, $ttl);
            return $buffer;
        });
    }
}

// ETag caching
class ETagMiddleware
{
    public function handle(callable $next): void
    {
        ob_start();
        $next();
        $content = ob_get_clean();

        $etag = md5($content);
        header("ETag: \"{$etag}\"");

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
            && trim($_SERVER['HTTP_IF_NONE_MATCH']) === "\"{$etag}\""
        ) {
            http_response_code(304);
            exit;
        }

        echo $content;
    }
}
```

---

## 10.2 Exercises

1. Implement response caching with configurable TTL
2. Add ETag support for conditional requests
3. Set up Redis caching for database queries
4. Configure CDN cache headers for static content

---

## Further Reading

- **Doc:** [HTTP Caching](https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching)
