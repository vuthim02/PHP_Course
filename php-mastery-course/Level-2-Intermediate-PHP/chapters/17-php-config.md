# Chapter 17: PHP Configuration and Performance

## Learning Objectives

- Optimize php.ini settings
- Configure OPcache
- Profile PHP applications
- Apply performance best practices

---

## 17.1 PHP Configuration

```ini
; Production php.ini optimization
; ❗ IMPORTANT: Settings for production
memory_limit = 256M
max_execution_time = 30
max_input_time = 60
upload_max_filesize = 64M
post_max_size = 64M
date.timezone = UTC

; Error reporting (hide in production)
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

; Security
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
expose_php = Off
allow_url_fopen = On
allow_url_include = Off

; Sessions
session.gc_maxlifetime = 1440
session.save_path = /tmp/sessions
```

---

## 17.2 OPcache Configuration

```ini
[opcache]
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
opcache.validate_timestamps = 0  ; Production: disable in deployment
opcache.jit_buffer_size = 100M
opcache.jit = tracing
```

---

## 17.3 Performance Monitoring

```php
<?php
class PerformanceMonitor
{
    private array $measurements = [];

    public function start(string $label): void
    {
        $this->measurements[$label] = [
            'start' => hrtime(true),
            'memory' => memory_get_usage(),
        ];
    }

    public function end(string $label): array
    {
        $start = $this->measurements[$label];
        $result = [
            'label' => $label,
            'time_ms' => (hrtime(true) - $start['start']) / 1_000_000,
            'memory_kb' => (memory_get_usage() - $start['memory']) / 1024,
            'peak_memory_kb' => memory_get_peak_usage() / 1024,
        ];
        $this->results[] = $result;
        return $result;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function logSlowQueries(float $threshold = 100): void
    {
        foreach ($this->results as $result) {
            if ($result['time_ms'] > $threshold) {
                error_log(
                    "SLOW: {$result['label']} took {$result['time_ms']}ms"
                );
            }
        }
    }
}

// Usage
$monitor = new PerformanceMonitor();
$monitor->start('database_query');
$users = User::all();
$result = $monitor->end('database_query');
```

---

## 17.4 Exercises

1. Optimize php.ini for a production application
2. Enable OPcache and JIT, measure before/after performance
3. Profile a page request and identify the 3 slowest operations
4. Implement a performance monitoring middleware

---

## Further Reading

- **Doc:** [PHP OPcache](https://www.php.net/manual/en/book.opcache.php)
- **Doc:** [PHP JIT](https://www.php.net/manual/en/jit.configuration.php)
