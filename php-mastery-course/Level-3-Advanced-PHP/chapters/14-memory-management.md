# Chapter 14: Memory Management

## Learning Objectives

- Understand PHP memory model
- Handle circular references
- Monitor memory usage
- Optimize memory in applications

---

## 14.1 Memory Basics

```php
<?php
// Memory usage monitoring
class MemoryDebugger
{
    public static function snapshot(string $label = ''): array
    {
        return [
            'label' => $label,
            'usage' => memory_get_usage(),
            'peak' => memory_get_peak_usage(),
            'real_usage' => memory_get_usage(true),
            'real_peak' => memory_get_peak_usage(true),
        ];
    }

    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

echo MemoryDebugger::formatBytes(memory_get_usage());
// e.g., "2.35 MB"

// Large data handling
$start = memory_get_usage();

// ❌ Memory-heavy: loading everything at once
$largeArray = range(1, 100000);
echo MemoryDebugger::formatBytes(memory_get_usage() - $start); // ~8 MB

// ✅ Memory-efficient: using generator
function rangeGenerator(int $start, int $end): Generator
{
    for ($i = $start; $i <= $end; $i++) {
        yield $i;
    }
}

$start = memory_get_usage();
$generator = rangeGenerator(1, 100000);
echo MemoryDebugger::formatBytes(memory_get_usage() - $start); // ~0 KB
```

---

## 14.2 Circular References

```php
<?php
// Circular references prevent garbage collection
class Node
{
    public ?Node $parent = null;
    /** @var Node[] */
    public array $children = [];

    public function __construct(
        public string $name
    ) {}

    public function addChild(Node $child): void
    {
        $this->children[] = $child;
        $child->parent = $this;  // Creates circular reference
    }
}

// Creating a circular reference tree
$root = new Node('root');
$child = new Node('child');
$grandchild = new Node('grandchild');

$root->addChild($child);
$child->addChild($grandchild);

// unset($root); // Memory leak without GC cycle collection

// PHP's garbage collector handles circular references
// but only when gc.enabled or gc_collect_cycles() is called
echo gc_collect_cycles(); // Returns number of collected cycles

// Disable/enable GC
gc_disable();
// ... code that creates many cycles ...
gc_enable();

// Force collection
$collected = gc_collect_cycles();
echo "Collected: {$collected} cycles\n";

// Memory limit
ini_set('memory_limit', '256M');
echo ini_get('memory_limit'); // 256M
```

---

## 14.3 Exercises

1. Write a script that creates 100K objects and measures memory usage
2. Create a circular reference structure and measure the leak
3. Implement a memory-efficient CSV parser using generators
4. Monitor peak memory usage in a long-running script

---

## Further Reading

- **Doc:** [PHP Garbage Collection](https://www.php.net/manual/en/features.gc.php)
- **Doc:** [Memory Functions](https://www.php.net/manual/en/ref.info.php)
