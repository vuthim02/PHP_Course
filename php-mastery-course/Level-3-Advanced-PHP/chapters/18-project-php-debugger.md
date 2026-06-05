# Chapter 18: Project: PHP Debugger and Profiler

## Project Overview

Build a custom debugging and profiling tool that tracks execution time, memory usage, database queries, and provides detailed performance insights.

---

## 18.1 Requirements

### Features
- Execution time tracking
- Memory usage monitoring
- Database query logging
- Variable dumper
- Error and exception capture
- Performance report generation
- Timeline visualization
- Recursive function tracing

### Technical Stack
- Pure PHP
- Reflection API
- Output buffering
- Error handling functions

---

## 18.2 Core Implementation

```php
<?php
namespace App\Debug;

class Debugger
{
    private static ?Debugger $instance = null;
    private array $measurements = [];
    private array $queries = [];
    private array $errors = [];
    private array $dumpData = [];
    private float $startTime;
    private int $startMemory;

    private function __construct()
    {
        $this->startTime = hrtime(true);
        $this->startMemory = memory_get_usage();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function start(string $label): void
    {
        $this->measurements[$label] = [
            'start' => hrtime(true),
            'start_memory' => memory_get_usage(),
            'queries_before' => count($this->queries),
        ];
    }

    public function end(string $label): array
    {
        $measurement = $this->measurements[$label];
        $result = [
            'label' => $label,
            'time_ms' => round((hrtime(true) - $measurement['start']) / 1_000_000, 2),
            'memory_kb' => round(
                (memory_get_usage() - $measurement['start_memory']) / 1024, 2
            ),
            'queries' => count($this->queries) - $measurement['queries_before'],
        ];
        
        $this->measurements[$label]['result'] = $result;
        return $result;
    }

    public function logQuery(string $sql, array $params, float $time): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params,
            'time_ms' => round($time, 2),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3),
        ];
    }

    public function logError(\Throwable $e): void
    {
        $this->errors[] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'time' => date('Y-m-d H:i:s'),
        ];
    }

    public function dump(string $label, mixed $value): void
    {
        $this->dumpData[] = [
            'label' => $label,
            'value' => $value,
            'type' => gettype($value),
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2),
        ];
    }

    public function generateReport(): string
    {
        $totalTime = round((hrtime(true) - $this->startTime) / 1_000_000, 2);
        $peakMemory = round(memory_get_peak_usage(true) / 1024, 2);
        $finalMemory = round((memory_get_usage() - $this->startMemory) / 1024, 2);

        $html = '<style>
            .debug-panel { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; margin: 20px; border-radius: 8px; }
            .debug-panel table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            .debug-panel th, .debug-panel td { text-align: left; padding: 8px; border-bottom: 1px solid #333; }
            .debug-panel .slow { color: #ff6b6b; }
            .debug-panel .ok { color: #69db7c; }
        </style>';

        $html .= '<div class="debug-panel">';
        $html .= '<h2>Debug Report</h2>';
        $html .= "<p>Total Time: <strong>{$totalTime}ms</strong></p>";
        $html .= "<p>Peak Memory: <strong>{$peakMemory}KB</strong></p>";
        $html .= "<p>Final Memory: <strong>{$finalMemory}KB</strong></p>";

        // Measurements
        $html .= '<h3>Timeline</h3><table><tr><th>Label</th><th>Time</th><th>Memory</th><th>Queries</th></tr>';
        foreach ($this->measurements as $m) {
            $r = $m['result'] ?? null;
            if (!$r) continue;
            $color = $r['time_ms'] > 100 ? 'slow' : 'ok';
            $html .= "<tr class='{$color}'>
                <td>{$r['label']}</td>
                <td>{$r['time_ms']}ms</td>
                <td>{$r['memory_kb']}KB</td>
                <td>{$r['queries']}</td>
            </tr>";
        }
        $html .= '</table>';

        // Queries
        $html .= '<h3>Database Queries</h3><table><tr><th>SQL</th><th>Time</th></tr>';
        foreach ($this->queries as $q) {
            $html .= "<tr><td>{$q['sql']}</td><td>{$q['time_ms']}ms</td></tr>";
        }
        $html .= '</table>';

        // Dumps
        $html .= '<h3>Variable Dumps</h3>';
        foreach ($this->dumpData as $d) {
            $html .= "<div><strong>{$d['label']}</strong> ({$d['type']}): ";
            $html .= '<pre>' . htmlspecialchars(print_r($d['value'], true)) . '</pre></div>';
        }

        $html .= '</div>';
        return $html;
    }
}

// PDO wrapper that logs queries
class DebugPDO extends \PDO
{
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): \PDOStatement|false
    {
        $start = hrtime(true);
        $result = parent::query($query, $fetchMode, ...$fetchModeArgs);
        $time = (hrtime(true) - $start) / 1_000_000;
        Debugger::getInstance()->logQuery($query, [], $time);
        return $result;
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false
    {
        $statement = parent::prepare($query, $options);
        return new DebugPDOStatement($statement);
    }
}

class DebugPDOStatement
{
    public function __construct(private \PDOStatement $statement) {}

    public function execute(?array $params = null): bool
    {
        $start = hrtime(true);
        $result = $this->statement->execute($params);
        $time = (hrtime(true) - $start) / 1_000_000;
        Debugger::getInstance()->logQuery(
            $this->statement->queryString,
            $params ?? [],
            $time
        );
        return $result;
    }

    public function __call(string $name, array $args): mixed
    {
        return $this->statement->$name(...$args);
    }
}
```

---

## 18.3 Usage Example

```php
<?php
require_once 'vendor/autoload.php';

use App\Debug\Debugger;
use App\Debug\DebugPDO;

$debug = Debugger::getInstance();

// Wrap PDO
$pdo = new DebugPDO('mysql:host=localhost;dbname=test', 'root', '');

$debug->start('page_generation');

$debug->start('user_query');
$users = $pdo->query('SELECT * FROM users LIMIT 10')->fetchAll();
$debug->end('user_query');

$debug->start('post_query');
$posts = $pdo->query('SELECT * FROM posts WHERE user_id = 1')->fetchAll();
$debug->end('post_query');

$debug->dump('users', $users);
$debug->end('page_generation');

// Output report
if ($_ENV['APP_DEBUG'] ?? false) {
    echo $debug->generateReport();
}
```

---

## 18.4 Deliverables

1. Custom debug panel showing performance metrics
2. PDO wrapper that logs all queries
3. Variable dumper for inspection
4. Error capture and reporting
5. Performance report generation

---

## Further Reading

- **Doc:** [PHP Reflection](https://www.php.net/manual/en/book.reflection.php)
- **Doc:** [Debug Backtrace](https://www.php.net/manual/en/function.debug-backtrace.php)
