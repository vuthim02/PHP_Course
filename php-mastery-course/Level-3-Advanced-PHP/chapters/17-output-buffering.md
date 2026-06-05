# Chapter 17: Advanced Output Buffering

## Learning Objectives

- Master nested output buffering
- Implement callbacks with ob_start
- Use output buffering for compression
- Build response manipulation tools

---

## 17.1 Nested Buffers

```php
<?php
class OutputBufferManager
{
    private int $level = 0;

    public function start(): void
    {
        ob_start();
        $this->level = ob_get_level();
    }

    public function capture(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean();
    }

    public function getContents(): string
    {
        $content = '';
        while (ob_get_level() > $this->level) {
            $content = ob_get_clean() . $content;
        }
        return $content;
    }

    public function flushAll(): void
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    public static function getBufferInfo(): array
    {
        return [
            'level' => ob_get_level(),
            'length' => ob_get_length(),
            'status' => ob_get_status(true),
        ];
    }
}

// Usage
$buffer = new OutputBufferManager();
$buffer->start();

echo "Outer content\n";
$nested = $buffer->capture(function () {
    echo "Nested content\n";
});
echo "More outer content\n";

$all = $buffer->getContents();
```

---

## 17.2 Callbacks and Compression

```php
<?php
// Minify HTML output
class HtmlMinifier
{
    public static function minify(string $html): string
    {
        // Remove comments
        $html = preg_replace('/<!--[^>]*-->/', '', $html);
        // Remove whitespace between tags
        $html = preg_replace('/>\s+</', '><', $html);
        // Remove extra whitespace
        $html = preg_replace('/\s{2,}/', ' ', $html);
        return trim($html);
    }
}

// Start output buffering with callback
ob_start(function (string $buffer, int $phase): string {
    // Only modify on final flush
    if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
        return HtmlMinifier::minify($buffer);
    }
    return $buffer;
});

// Gzip compression
if (extension_loaded('zlib') && !ob_start('ob_gzhandler')) {
    ob_start();
}

// Cache entire output
class OutputCache
{
    private static array $cache = [];

    public static function start(string $key, int $ttl = 3600): bool
    {
        if (isset(self::$cache[$key])) {
            $cached = self::$cache[$key];
            if ($cached['expires'] > time()) {
                echo $cached['content'];
                return true; // Cache hit
            }
        }

        ob_start();
        self::$cache[$key] = ['expires' => time() + $ttl, 'content' => ''];

        return false; // Cache miss
    }

    public static function end(string $key): void
    {
        if (isset(self::$cache[$key])) {
            self::$cache[$key]['content'] = ob_get_clean();
            echo self::$cache[$key]['content'];
        }
    }
}

// Usage
if (!OutputCache::start('homepage', 300)) {
    // Generate page content
    echo "This content will be cached";
    OutputCache::end('homepage');
}
```

---

## 17.3 Exercises

1. Implement HTML minification using output buffering
2. Create a response compression layer with ob_gzhandler
3. Build an output cache that caches entire page responses
4. Implement response modification (add headers, modify HTML)

---

## Further Reading

- **Doc:** [PHP Output Buffering](https://www.php.net/manual/en/book.outcontrol.php)
