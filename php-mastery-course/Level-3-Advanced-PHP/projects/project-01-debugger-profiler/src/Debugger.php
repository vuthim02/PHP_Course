<?php

declare(strict_types=1);

namespace DebuggerProfiler;

use DebuggerProfiler\collectors\MemoryCollector;

class Debugger
{
    private static ?Debugger $instance = null;
    private bool $enabled = false;
    private array $logged = [];

    final public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    final protected function __construct() {}

    public function enable(): void
    {
        if ($this->enabled) {
            return;
        }

        $this->enabled = true;
        ob_start();

        ErrorHandler::getInstance()->register();

        register_shutdown_function(function (): void {
            if ($this->enabled) {
                $this->renderToolbar();
            }
        });
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function log(mixed $value, string $label = ''): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $this->logged[] = [
            'label' => $label,
            'value' => $value,
            'file'  => $trace['file'] ?? 'unknown',
            'line'  => $trace['line'] ?? 0,
            'time'  => microtime(true),
        ];
    }

    public function getLog(): array
    {
        return $this->logged;
    }

    public static function dd(mixed ...$vars): never
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        echo "<div style='font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4'>\n";
        echo "<h1 style='color:#569cd6'>dd() — Dump & Die</h1>\n";

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        echo "<p style='color:#888'>" . htmlspecialchars("{$trace['file']}:{$trace['line']}", ENT_QUOTES, 'UTF-8') . "</p>\n";

        foreach ($vars as $i => $var) {
            echo "<h3 style='color:#c586c0'>Variable #$i</h3>\n";
            echo "<pre style='background:#252526;padding:12px;border-radius:4px;overflow:auto'>";
            echo Dump::dump($var);
            echo "</pre>\n";
        }

        echo "</div>\n";
        exit(1);
    }

    private function renderToolbar(): void
    {
        $content = ob_get_clean();
        $profiler = Profiler::getInstance()->getReport();
        $time = round($profiler['execution_time'] * 1000, 2);
        $mem  = $profiler['peak_memory_fmt'];
        $queries = $profiler['query_count'];
        $logCount = count($this->logged);

        $toolbar = <<<HTML
<div id="php-debug-toolbar" style="
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #2d2d2d; color: #ccc;
    font-family: monospace; font-size: 12px;
    padding: 6px 12px; z-index: 99999;
    display: flex; gap: 20px; align-items: center;
    border-top: 2px solid #007bff;
">
    <span style="color:#007bff;font-weight:bold">PHP Debugger</span>
    <span>Time: {$time}ms</span>
    <span>Mem: {$mem}</span>
    <span>Queries: {$queries}</span>
    <span>Logs: {$logCount}</span>
</div>
HTML;

        echo $content . $toolbar;
    }

    public static function benchmark(callable $callback, int $iterations = 1000): array
    {
        $start = microtime(true);
        $memStart = memory_get_usage(true);

        for ($i = 0; $i < $iterations; $i++) {
            $callback();
        }

        $elapsed = microtime(true) - $start;
        $memUsed = memory_get_usage(true) - $memStart;

        return [
            'total_time'   => round($elapsed, 6),
            'avg_time'     => round($elapsed / $iterations, 8),
            'iterations'   => $iterations,
            'memory_used'  => $memUsed,
            'memory_fmt'   => (new MemoryCollector())->formatBytes($memUsed),
            'ops_per_sec'  => $elapsed > 0 ? round($iterations / $elapsed, 2) : INF,
        ];
    }

    private function __clone(): void {}
    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }
}
