# Chapter 18: Caching in PHP

## Learning Objectives

- Implement file-based caching
- Use APCu for opcode caching
- Cache with Redis and Memcached
- Apply cache invalidation strategies

---

## 18.1 Cache Abstraction

```php
<?php
interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 3600): void;
    public function delete(string $key): void;
    public function clear(): void;
    public function has(string $key): bool;
}

class FileCache implements CacheInterface
{
    public function __construct(
        private string $directory = '/tmp/cache'
    ) {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755, true);
        }
    }

    private function getPath(string $key): string
    {
        return "{$this->directory}/" . md5($key) . '.cache';
    }

    public function get(string $key): mixed
    {
        $path = $this->getPath($key);
        if (!file_exists($path)) {
            return null;
        }

        $data = unserialize(file_get_contents($path));
        if ($data['expires'] < time()) {
            unlink($path);
            return null;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $data = serialize([
            'value' => $value,
            'expires' => time() + $ttl,
        ]);
        file_put_contents($this->getPath($key), $data);
    }

    public function delete(string $key): void
    {
        $path = $this->getPath($key);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function clear(): void
    {
        array_map('unlink', glob("{$this->directory}/*.cache"));
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}

class RedisCache implements CacheInterface
{
    public function __construct(
        private Redis $redis
    ) {}

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : null;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $this->redis->setex($key, $ttl, serialize($value));
    }

    public function delete(string $key): void
    {
        $this->redis->del($key);
    }

    public function clear(): void
    {
        $this->redis->flushAll();
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }
}
```

---

## 18.2 Cache Strategies

```php
<?php
class CacheService
{
    public function __construct(private CacheInterface $cache) {}

    // Cache-Aside (Lazy Loading)
    public function getExpensiveData(int $id): array
    {
        $key = "data:{$id}";
        
        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $data = $this->fetchFromDatabase($id);
        $this->cache->set($key, $data, 3600);
        
        return $data;
    }

    // Write-Through
    public function saveUser(array $userData): User
    {
        $user = $this->saveToDatabase($userData);
        $this->cache->set("user:{$user->id}", $user, 3600);
        $this->cache->delete('users:list');
        return $user;
    }

    // Cache Invalidation
    public function invalidateUser(int $userId): void
    {
        $this->cache->delete("user:{$userId}");
        $this->cache->delete("user:{$userId}:posts");
        $this->cache->delete("user:{$userId}:followers");
    }
}
```

---

## 18.3 Exercises

1. Implement file-based caching for database query results
2. Install Redis and implement a Redis cache driver
3. Add cache invalidation when data is updated
4. Benchmark file cache vs Redis vs APCu

---

## Further Reading

- **Doc:** [Redis PHP](https://redis.io/docs/latest/develop/connect/clients/php/)
- **Doc:** [APCu Documentation](https://www.php.net/manual/en/book.apcu.php)
