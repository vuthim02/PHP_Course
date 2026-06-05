<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DebuggerProfiler\Debugger;
use DebuggerProfiler\Profiler;

Debugger::getInstance()->enable();

try {
    echo "=== PHP Debugger & Profiler Demo ===\n\n";

    Profiler::measure(function () {
        $data = array_map(fn(int $n) => $n ** 2, range(1, 10000));
        Debugger::getInstance()->log($data, 'squared_numbers');
    }, 'array_map_operation');

    Profiler::getInstance()->time()->mark('before_sort');
    $arr = range(1, 5000);
    shuffle($arr);
    sort($arr);
    Profiler::getInstance()->time()->mark('after_sort');

    Profiler::getInstance()->memory()->snapshot('after_sort');

    Debugger::getInstance()->log(Profiler::getInstance()->getReport(), 'profiler_report');
    Profiler::getInstance()->queries()->log('SELECT * FROM users WHERE id = ?', [1], 0.0032);
    Profiler::getInstance()->queries()->log('SELECT p.*, u.name FROM posts p JOIN users u ON u.id = p.user_id WHERE p.active = ?', [1], 0.015);

    $report = Profiler::getInstance()->getReport();

    echo "Execution time: " . round($report['execution_time'] * 1000, 2) . "ms\n";
    echo "Peak memory: {$report['peak_memory_fmt']}\n";
    echo "Queries logged: {$report['query_count']}\n";
    echo "Query total time: " . round($report['query_time'] * 1000, 2) . "ms\n";
    echo "\n";

    $bench = Debugger::benchmark(fn() => strlen('hello world'), 10000);
    echo "Benchmark (10000 iterations):\n";
    echo "  Total: {$bench['total_time']}s\n";
    echo "  Avg: {$bench['avg_time']}s\n";
    echo "  Ops/sec: {$bench['ops_per_sec']}\n";
    echo "  Memory: {$bench['memory_fmt']}\n";

    echo "\nDemo completed successfully!\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
