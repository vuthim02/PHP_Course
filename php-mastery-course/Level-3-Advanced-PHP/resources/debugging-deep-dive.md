# Advanced Debugging — Deep Dive

## Xdebug Setup

```ini
; php.ini — Full Xdebug configuration
zend_extension=xdebug

; Debug mode
xdebug.mode=debug,develop,trace,profile
xdebug.start_with_request=yes

; IDE communication
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.idekey=PHPSTORM

; Output
xdebug.output_dir=/tmp/xdebug
xdebug.collect_params=4
xdebug.collect_return=1
xdebug.show_local_vars=1

; Performance
xdebug.max_nesting_level=512
xdebug.var_display_max_depth=6
xdebug.var_display_max_children=256
xdebug.var_display_max_data=1024
```

## Step Debugging with VS Code

Create `.vscode/launch.json`:
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            },
            "log": true
        },
        {
            "name": "Launch Built-in Server",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "runtimeArgs": [
                "-S",
                "localhost:8000",
                "-t",
                "public"
            ],
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            }
        }
    ]
}
```

### Step Debugging Commands

| Action | VS Code | PHPStorm |
|--------|---------|----------|
| Toggle breakpoint | F9 | Ctrl+F8 |
| Continue | F5 | F9 |
| Step Over | F10 | F8 |
| Step Into | F11 | F7 |
| Step Out | Shift+F11 | Shift+F8 |
| Evaluate expression | Ctrl+Shift+I | Alt+F8 |
| Watch variable | Hover | Add to watches |

## Tracing

```php
// Function traces with Xdebug
xdebug_start_trace('/tmp/trace/trace_output');
slowFunction();
xdebug_stop_trace();
// Produces a trace file with every function call, param, and return value

// For large apps, selectively trace
xdebug_start_trace('/tmp/trace/trace', XDEBUG_TRACE_NAKED);
// ... only the code between start and stop
xdebug_stop_trace();
```

## Analyzing with Xdebug + QCacheGrind / KCacheGrind

```bash
# Generate profile
xdebug.mode=profile
# Run your script → produces cachegrind.out.* file in /tmp

# View with KCacheGrind (Linux)
kcachegrind /tmp/cachegrind.out.12345

# View with QCacheGrind (macOS/Windows)
qcachegrind /tmp/cachegrind.out.12345
```

## Memory Debugging

```php
// Track memory usage
$startMem = memory_get_usage();
expensiveOperation();
$endMem = memory_get_usage();
echo "Used: " . ($endMem - $startMem) / 1024 . "KB";

// Peak memory
echo "Peak: " . memory_get_peak_usage(true) / 1024 / 1024 . "MB";

// Detect memory leaks
class LeakDetector {
    private static array $instances = [];

    public function __construct() {
        self::$instances[] = $this;  // Never freed! Leak!
    }
}

// Use debug_zval_refs to inspect reference counts
$a = new stdClass();
debug_zval_refs($a);  // Shows refcount
$b = $a;
debug_zval_refs($a);  // Refcount increased
```

## Backtrace Analysis

```php
function debug_backtrace_custom(): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
    echo '<pre>';
    foreach ($trace as $i => $frame) {
        printf(
            "#%d %s(%s): %s%s%s()\n",
            $i,
            $frame['file'] ?? '[internal]',
            $frame['line'] ?? '',
            $frame['class'] ?? '',
            $frame['type'] ?? '',
            $frame['function'] ?? ''
        );
    }
    echo '</pre>';
}

// Exception backtrace
try {
    throw new RuntimeException('Something broke');
} catch (RuntimeException $e) {
    echo $e->getTraceAsString();
    // 0. /path/to/file.php:10
    // 1. /path/to/index.php:5
}
```

## Debugging Production Issues

### 1. Strace (no code changes needed)
```bash
# Trace system calls of a running PHP process
sudo strace -p $(pgrep -n php-fpm) -e network,syscall -o /tmp/strace.log

# Monitor file access
sudo strace -e openat,open -p $(pgrep -n php-fpm)
```

### 2. PHP-FPM Status Page
```nginx
location /fpm-status {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME /fpm-status;
}

location /fpm-ping {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME /fpm-ping;
}
```

### 3. Slow Log
```ini
; php-fpm.conf
request_slowlog_timeout = 5     ; Log requests > 5 seconds
request_slowlog_file = /var/log/php-fpm-slow.log
slowlog = /var/log/php-fpm-slow.log
```

### 4. Memory Profiling with valgrind
```bash
# Find memory leaks in PHP-CLI
valgrind --tool=memcheck --leak-check=full php script.php

# Massif for heap profiling
valgrind --tool=massif php script.php
ms_print massif.out.*
```

## Error Stack Trace Enhancement

```php
// Custom error handler with full backtrace
set_error_handler(function (
    int $severity,
    string $message,
    string $file,
    int $line
): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2);
    $backtrace = array_map(fn($frame) => sprintf(
        '%s:%s — %s%s%s()',
        $frame['file'] ?? '[internal]',
        $frame['line'] ?? '?',
        $frame['class'] ?? '',
        $frame['type'] ?? '',
        $frame['function'] ?? ''
    ), $trace);

    error_log(sprintf(
        "[%s] %s in %s:%d\nBacktrace:\n  %s",
        date('Y-m-d H:i:s'),
        $message,
        $file,
        $line,
        implode("\n  ", $backtrace)
    ));

    return true;
});

// Set exception handler
set_exception_handler(function (\Throwable $e): void {
    http_response_code(500);
    if ($_ENV['APP_ENV'] === 'dev') {
        echo '<h1>Exception</h1>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        echo 'An error occurred. Please try again.';
    }
    error_log((string) $e);
});
```

## Debugging Race Conditions

```php
// Use microtime for precise timing
$time = microtime(true);
$pid = getmypid();
$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
$caller = $trace[1]['function'] ?? 'main';

error_log(sprintf(
    "[%s][PID:%d][%s] Before lock",
    number_format($time, 6),
    $pid,
    $caller
));

// File locking debug
$fp = fopen('/tmp/lock', 'w');
if (flock($fp, LOCK_EX)) {
    error_log("Lock acquired by PID $pid");
    // Critical section
    sleep(1);
    flock($fp, LOCK_UN);
    error_log("Lock released by PID $pid");
}
```

## Network Debugging

```php
// Debug HTTP requests
$ch = curl_init('https://api.example.com');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => fopen('php://temp', 'w+'),
]);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
$verbose = stream_get_contents($ch);
echo '<pre>';
print_r($info);
echo htmlspecialchars($verbose);
echo '</pre>';
```
