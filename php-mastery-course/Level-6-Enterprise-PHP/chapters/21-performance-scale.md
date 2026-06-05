# Chapter 21: Performance at Scale

## Learning Objectives

- Implement load testing
- Configure auto-scaling
- Optimize database at scale
- Use CDN and edge computing

---

## 21.1 Load Testing

```php
<?php
// Simple load test script
class LoadTest
{
    private array $results = [];

    public function __construct(
        private string $url,
        private int $concurrency = 10,
        private int $requests = 100,
    ) {}

    public function run(): array
    {
        $start = microtime(true);
        $completed = 0;
        $errors = 0;
        $times = [];

        // Use multiple cURL handles for concurrent requests
        $multiHandle = curl_multi_init();
        $handles = [];

        for ($i = 0; $i < min($this->concurrency, $this->requests); $i++) {
            $handles[] = $this->createHandle();
        }

        do {
            curl_multi_exec($multiHandle, $running);
            
            while ($info = curl_multi_info_read($multiHandle)) {
                $handle = $info['handle'];
                $time = curl_getinfo($handle, CURLINFO_TOTAL_TIME) * 1000;
                $times[] = $time;
                
                if ($info['result'] !== CURLE_OK) {
                    $errors++;
                }
                
                $completed++;
                curl_multi_remove_handle($multiHandle, $handle);
                curl_close($handle);
                
                if ($completed + count($handles) <= $this->requests) {
                    $newHandle = $this->createHandle();
                    $handles[] = $newHandle;
                    curl_multi_add_handle($multiHandle, $newHandle);
                }
            }
        } while ($running || $completed < $this->requests);

        curl_multi_close($multiHandle);

        $total = microtime(true) - $start;
        sort($times);

        return [
            'url' => $this->url,
            'total_requests' => $this->requests,
            'concurrency' => $this->concurrency,
            'total_time' => round($total, 2) . 's',
            'requests_per_second' => round($this->requests / $total, 2),
            'avg_response_time' => round(array_sum($times) / count($times), 2) . 'ms',
            'p50' => round($times[(int)(count($times) * 0.5)], 2) . 'ms',
            'p95' => round($times[(int)(count($times) * 0.95)], 2) . 'ms',
            'p99' => round($times[(int)(count($times) * 0.99)], 2) . 'ms',
            'errors' => $errors,
            'error_rate' => round($errors / $this->requests * 100, 2) . '%',
        ];
    }

    private function createHandle()
    {
        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        return $ch;
    }
}

// Usage
$test = new LoadTest('https://api.example.com/users', concurrency: 50, requests: 1000);
$results = $test->run();
print_r($results);
```

---

## 21.2 Exercises

1. Run a load test against an API endpoint
2. Configure auto-scaling based on CPU/memory/requests
3. Set up CDN for static assets and API responses
4. Optimize database queries at high concurrency

---

## Further Reading

- **Tool:** [JMeter](https://jmeter.apache.org/)
- **Tool:** [k6](https://k6.io/)
- **Doc:** [Auto Scaling](https://aws.amazon.com/autoscaling/)
