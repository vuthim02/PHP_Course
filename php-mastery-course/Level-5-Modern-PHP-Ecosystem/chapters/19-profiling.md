# Chapter 19: Profiling and Benchmarking

## Learning Objectives

- Profile PHP applications with Xdebug
- Benchmark code performance
- Use Blackfire for deep profiling
- Identify and fix bottlenecks

---

## 19.1 Profiling Tools

```php
<?php
// Basic benchmarking
class Benchmark
{
    private array $results = [];

    public function measure(string $label, callable $fn, int $iterations = 1000): array
    {
        $times = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            $start = hrtime(true);
            $fn();
            $times[] = (hrtime(true) - $start) / 1_000_000; // ms
        }

        $avg = array_sum($times) / count($times);
        $min = min($times);
        $max = max($times);

        return [
            'label' => $label,
            'iterations' => $iterations,
            'avg_ms' => round($avg, 4),
            'min_ms' => round($min, 4),
            'max_ms' => round($max, 4),
            'total_ms' => round(array_sum($times), 2),
        ];
    }

    public function compare(array $benchmarks): void
    {
        echo str_repeat('-', 80) . "\n";
        echo sprintf("%-30s %12s %12s %12s\n", 'Method', 'Avg (ms)', 'Min (ms)', 'Max (ms)');
        echo str_repeat('-', 80) . "\n";

        foreach ($benchmarks as $bench) {
            echo sprintf(
                "%-30s %12.4f %12.4f %12.4f\n",
                $bench['label'],
                $bench['avg_ms'],
                $bench['min_ms'],
                $bench['max_ms']
            );
        }
        echo str_repeat('-', 80) . "\n";
    }
}

// Usage
$bench = new Benchmark();

// Compare string concatenation methods
$benchmarks = [];

$benchmarks[] = $bench->measure('String concatenation', function () {
    $result = '';
    for ($i = 0; $i < 100; $i++) {
        $result .= "item_{$i},";
    }
}, 1000);

$benchmarks[] = $bench->measure('Array implode', function () {
    $parts = [];
    for ($i = 0; $i < 100; $i++) {
        $parts[] = "item_{$i}";
    }
    implode(',', $parts);
}, 1000);

$bench->compare($benchmarks);
```

---

## 19.2 Exercises

1. Profile a page request with Xdebug and analyze the cachegrind file
2. Benchmark different sorting algorithms
3. Compare performance of different cache drivers (file vs Redis vs APCu)
4. Use Blackfire to profile a full page load and identify bottlenecks

---

## Further Reading

- **Tool:** [Blackfire](https://blackfire.io/)
- **Tool:** [Xdebug Profiler](https://xdebug.org/docs/profiler)
- **Tool:** [JMeter](https://jmeter.apache.org/)
