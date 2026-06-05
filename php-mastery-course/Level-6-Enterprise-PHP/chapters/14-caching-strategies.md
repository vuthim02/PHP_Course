# Chapter 14: Caching Strategies (Multi-Level)

## Learning Objectives

- Implement multi-level caching
- Manage cache invalidation
- Use write-through and write-behind
- Handle cache stampedes

---

## 14.1 Multi-Level Cache

```php
<?php
class MultiLevelCache
{
    private array $levels = [];

    public function addLevel(string $name, CacheInterface $cache, int $priority): void
    {
        $this->levels[] = compact('name', 'cache', 'priority');
        usort($this->levels, fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    public function get(string $key): mixed
    {
        foreach ($this->levels as $level) {
            $value = $level['cache']->get($key);
            if ($value !== null) {
                // Warm upper levels
                $this->warmUpperLevels($key, $value, $level['name']);
                return $value;
            }
        }
        return null;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        foreach ($this->levels as $level) {
            $level['cache']->set($key, $value, $ttl);
        }
    }

    public function delete(string $key): void
    {
        foreach ($this->levels as $level) {
            $level['cache']->delete($key);
        }
    }

    private function warmUpperLevels(string $key, mixed $value, string $fromLevel): void
    {
        foreach ($this->levels as $level) {
            if ($level['name'] === $fromLevel) {
                break;
            }
            $level['cache']->set($key, $value, 60); // Shorter TTL for upper levels
        }
    }
}

// Usage
$cache = new MultiLevelCache();
$cache->addLevel('l1_apcu', new APCuCache(), 1);      // Fastest
$cache->addLevel('l2_redis', new RedisCache(), 2);     // Fast
$cache->addLevel('l3_file', new FileCache(), 3);       // Slowest

// Cache stampede prevention
class StampedeProtection
{
    public function __construct(private CacheInterface $cache) {}

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->cache->get($key);
        
        if ($value !== null) {
            // Check if we should early-recompute
            if ($this->isExpiringSoon($key, $ttl)) {
                // Only one process should recompute
                $lockKey = "lock:{$key}";
                if ($this->acquireLock($lockKey)) {
                    try {
                        $newValue = $callback();
                        $this->cache->set($key, $newValue, $ttl);
                        return $newValue;
                    } finally {
                        $this->releaseLock($lockKey);
                    }
                }
            }
            return $value;
        }

        // Cache miss
        $lockKey = "lock:{$key}";
        if ($this->acquireLock($lockKey)) {
            try {
                $value = $callback();
                $this->cache->set($key, $value, $ttl);
                return $value;
            } finally {
                $this->releaseLock($lockKey);
            }
        }

        // Another process is computing, wait for it
        usleep(50000); // 50ms
        return $this->cache->get($key) ?? $callback();
    }

    private function acquireLock(string $key): bool
    {
        // Atomic lock with NX
        return $this->cache->set($key, true, 5); // 5 second lock
    }

    private function releaseLock(string $key): void
    {
        $this->cache->delete($key);
    }

    private function isExpiringSoon(string $key, int $ttl): bool
    {
        $remaining = $this->cache->ttl($key);
        return $remaining < ($ttl * 0.2); // 20% of TTL remaining
    }
}
```

---

## 14.2 Exercises

1. Implement multi-level cache (APCu → Redis → Database)
2. Add stampede protection for popular cache keys
3. Implement write-through cache for database writes
4. Benchmark hit rates across cache levels

---

## Further Reading

- **Doc:** [Caching Strategies](https://aws.amazon.com/caching/strategies/)
