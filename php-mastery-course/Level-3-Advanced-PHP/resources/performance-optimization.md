# PHP Performance Optimization — Reference Guide

## 1. OPcache (Lowest Hanging Fruit)

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=256       ; MB for cached scripts
opcache.interned_strings_buffer=16   ; MB for string interning
opcache.max_accelerated_files=20000  ; Max PHP files to cache
opcache.validate_timestamps=0        ; 0 in production, 1 in dev
opcache.revalidate_freq=0            ; Check file changes (sec)
opcache.enable_cli=0                 ; Usually not needed for CLI
opcache.jit=1255                     ; Enable JIT (PHP 8.0+)
opcache.jit_buffer_size=100M         ; JIT code buffer
```

## 2. JIT Compilation (PHP 8.0+)

```ini
; PHP 8.0+ Just-In-Time compiler
opcache.jit=1255        ; CRTO — CPU-specific optimization
opcache.jit_buffer_size=100M
```

```php
// Verify JIT is working
var_dump(opcache_get_status()['jit']);
// Flags: 1255 = CPU-specific + tracing
```

## 3. Profiling

### With Xdebug
```ini
; php.ini
xdebug.mode=profile
xdebug.output_dir=/tmp/profiling
```

### With Blackfire
```bash
# Install Blackfire agent and CLI
blackfire run php script.php
blackfire curl https://example.com
```

### With Tideways / Xhprof
```php
// Tideways (lightweight, production-safe)
tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_MEMORY);
runApplication();
$data = tideways_xhprof_disable();
// Store $data in a profiling database
```

## 4. Database Optimization

```php
// ❌ BAD: N+1 queries
$users = $pdo->query('SELECT * FROM users')->fetchAll();
foreach ($users as $user) {
    $posts = $pdo->query(
        "SELECT * FROM posts WHERE user_id = {$user['id']}"
    )->fetchAll();  // N queries!
}

// ✅ GOOD: Single query with JOIN
$users = $pdo->query('
    SELECT u.*, p.id as post_id, p.title
    FROM users u
    LEFT JOIN posts p ON p.user_id = u.id
')->fetchAll();

// ✅ GOOD: Batch loading
$userIds = array_column($users, 'id');
$placeholders = implode(',', array_fill(0, count($userIds), '?'));
$posts = $pdo->prepare("
    SELECT * FROM posts WHERE user_id IN ($placeholders)
")->execute($userIds);
$postsByUser = [];
foreach ($posts as $post) {
    $postsByUser[$post['user_id']][] = $post;
}
```

### Indexing

```sql
-- Always index foreign keys and frequently queried columns
CREATE INDEX idx_posts_user_id ON posts(user_id);
CREATE INDEX idx_posts_created_at ON posts(created_at);
CREATE INDEX idx_posts_user_status ON posts(user_id, status);  -- Composite

-- Use EXPLAIN ANALYZE to verify
EXPLAIN ANALYZE SELECT * FROM posts WHERE user_id = 5;
```

## 5. Caching Strategies

```php
// In-memory caching with array (single request)
$cache = [];
function getExpensiveData(int $id): array
{
    global $cache;
    if (!isset($cache[$id])) {
        $cache[$id] = expensiveDatabaseQuery($id);
    }
    return $cache[$id];
}

// File-based caching
function getCached(string $key, callable $loader, int $ttl = 3600): mixed
{
    $path = "/tmp/cache/" . md5($key);
    if (file_exists($path) && (time() - filemtime($path)) < $ttl) {
        return unserialize(file_get_contents($path));
    }
    $value = $loader();
    file_put_contents($path, serialize($value));
    return $value;
}

// Redis caching (production)
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

function cacheGet(string $key, callable $loader, int $ttl = 3600): mixed
{
    global $redis;
    $cached = $redis->get($key);
    if ($cached !== false) {
        return unserialize($cached);
    }
    $value = $loader();
    $redis->setex($key, $ttl, serialize($value));
    return $value;
}
```

## 6. Memory Optimization

```php
// Process large files line by line
$handle = fopen('huge.csv', 'r');
while (($row = fgetcsv($handle)) !== false) {
    processRow($row);  // Only one row in memory at a time
}
fclose($handle);

// Use generators for large datasets
function largeQuery(PDO $pdo): Generator
{
    $stmt = $pdo->query('SELECT * FROM millions_of_rows');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        yield $row;  // One row at a time
    }
}

foreach (largeQuery($pdo) as $row) {
    processRow($row);
}

// Free memory explicitly
$largeData = loadEverything();
process($largeData);
unset($largeData);  // Immediate GC hint

// Use SplFixedArray for fixed-size numeric arrays
$array = new SplFixedArray(1000000);
// Much more memory-efficient than regular array
```

## 7. String Operations

```php
// ❌ BAD: Building strings with concatenation
$result = '';
for ($i = 0; $i < 100000; $i++) {
    $result .= $i;  // Allocates new string every iteration
}

// ✅ GOOD: Build array then implode
$parts = [];
for ($i = 0; $i < 100000; $i++) {
    $parts[] = $i;
}
$result = implode('', $parts);

// Use strtr for multiple replacements (faster than str_replace)
$result = strtr($text, ['{{name}}' => $name, '{{date}}' => $date]);

// Use sprintf for complex formatting
$result = sprintf('User %s has %d items', $name, $count);
```

## 8. Loop Optimizations

```php
// Cache count
$count = count($items);
for ($i = 0; $i < $count; $i++) { ... }

// foreach is fastest for arrays
foreach ($items as $item) { ... }

// Avoid function calls in loop conditions
// ❌ BAD
for ($i = 0; $i < count($items); $i++) { ... }

// ✅ GOOD
$count = count($items);
for ($i = 0; $i < $count; $i++) { ... }

// Prefer array functions over loops
$doubled = array_map(fn($n) => $n * 2, $numbers);  // Faster in PHP 8+
```

## 9. Autoloading Optimization

```json
// composer.json — optimize autoloader
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        },
        "classmap": [
            "src/Commands/",   // Classes not following PSR-4
            "src/Legacy/"
        ],
        "files": [
            "src/functions.php"  // Function-only files
        ]
    },
    "scripts": {
        "post-autoload-dump": [
            "@php -r \"echo 'Optimizing...';\""
        ]
    }
}
```

```bash
# Generate optimized autoloader (classmap + authoritative)
composer dump-autoload --optimize
# Or for production:
composer install --no-dev --optimize-autoloader
```

## 10. PHP-FPM Tuning

```ini
; www.conf (PHP-FPM pool configuration)
pm = dynamic
pm.max_children = 50           ; Max PHP worker processes
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500          ; Restart workers after N requests

; Calculate max_children:
; Available RAM / (average PHP process memory)
; Example: 8GB RAM / 40MB per process = ~200
```

## 11. Benchmarking

```php
// Simple timing
$start = microtime(true);
// ... code to benchmark ...
$elapsed = (microtime(true) - $start) * 1000;
echo "Took {$elapsed}ms";

// Compare two approaches
function benchmark(callable $fn, int $iterations = 10000): float
{
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $fn();
    }
    return (microtime(true) - $start) * 1000;
}

$time1 = benchmark(fn() => slowOperation());
$time2 = benchmark(fn() => fastOperation());
echo "Slow: {$time1}ms, Fast: {$time2}ms";
```

## 12. Common Bottlenecks & Fixes

| Problem | Symptom | Fix |
|---------|---------|-----|
| N+1 queries | Page slow with lists | Eager-load related data |
| No indexes | Full table scans | Add indexes, use EXPLAIN |
| Large file handling | Memory exhausted | Stream/generator |
| Session locking | Concurrent requests blocked | Use Redis for sessions |
| Unoptimized autoloader | Slow first request | `composer dump-autoload -o` |
| Too many PHP-FPM workers | Server swap/thrash | Reduce `pm.max_children` |
| Slow external API | Page blocks on API call | Async processing, caching |
| No OPcache | High CPU on every request | Enable OPcache + JIT |

## Performance Checklist

- [ ] OPcache enabled with JIT (PHP 8+)
- [ ] Composer autoloader optimized
- [ ] Database queries have proper indexes
- [ ] N+1 queries eliminated
- [ ] Large files streamed, not loaded entirely
- [ ] Generators used for large datasets
- [ ] Redis/Memcached for hot data
- [ ] PHP-FPM pool tuned to server resources
- [ ] Session handler uses Redis (not files)
- [ ] CDN for static assets
- [ ] Response compression (gzip/brotli)
- [ ] Profiling done to identify actual bottlenecks
