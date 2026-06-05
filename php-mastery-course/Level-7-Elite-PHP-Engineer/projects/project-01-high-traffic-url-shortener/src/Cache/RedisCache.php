<?php

declare(strict_types=1);

namespace UrlShortener\Cache;

use Predis\ClientInterface;

final class RedisCache implements CacheInterface
{
    public function __construct(
        private readonly ClientInterface $redis,
        private readonly string $prefix = 'url:'
    ) {}

    public function get(string $key): mixed
    {
        $value = $this->redis->get($this->prefix . $key);
        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $encoded = is_string($value) ? $value : json_encode($value);
        $this->redis->setex($this->prefix . $key, $ttl, $encoded);
        return true;
    }

    public function delete(string $key): bool
    {
        $this->redis->del([$this->prefix . $key]);
        return true;
    }

    public function exists(string $key): bool
    {
        return (bool) $this->redis->exists($this->prefix . $key);
    }

    public function increment(string $key, int $amount = 1): int
    {
        return $this->redis->incrby($this->prefix . $key, $amount);
    }

    public function expire(string $key, int $ttl): bool
    {
        return $this->redis->expire($this->prefix . $key, $ttl);
    }
}
