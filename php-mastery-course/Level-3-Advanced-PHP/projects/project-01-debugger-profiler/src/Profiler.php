<?php

declare(strict_types=1);

namespace DebuggerProfiler;

use DebuggerProfiler\collectors\TimeCollector;
use DebuggerProfiler\collectors\MemoryCollector;
use DebuggerProfiler\collectors\QueryCollector;

class Profiler
{
    private static ?Profiler $instance = null;

    private TimeCollector $time;
    private MemoryCollector $memory;
    private QueryCollector $queries;
    private array $customData = [];

    final public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    final protected function __construct()
    {
        $this->time   = new TimeCollector();
        $this->memory = new MemoryCollector();
        $this->queries = new QueryCollector();
        $this->memory->snapshot('profiler_init');
    }

    public function time(): TimeCollector
    {
        return $this->time;
    }

    public function memory(): MemoryCollector
    {
        return $this->memory;
    }

    public function queries(): QueryCollector
    {
        return $this->queries;
    }

    public function addData(string $key, mixed $value): void
    {
        $this->customData[$key] = $value;
    }

    public function getCustomData(): array
    {
        return $this->customData;
    }

    public function getReport(): array
    {
        return [
            'execution_time' => $this->time->elapsed(),
            'peak_memory'    => $this->memory->peakUsage(),
            'peak_memory_fmt' => $this->memory->formatBytes($this->memory->peakUsage()),
            'time_marks'     => $this->time->getMarks(),
            'memory_snapshots' => $this->memory->getSnapshots(),
            'queries'        => $this->queries->getQueries(),
            'query_count'    => $this->queries->count(),
            'query_time'     => $this->queries->totalDuration(),
            'custom'         => $this->customData,
        ];
    }

    public function reset(): void
    {
        $this->time->reset();
        $this->queries->reset();
        $this->customData = [];
        $this->memory->snapshot('profiler_reset');
    }

    public static function measure(callable $callback, string $label = 'measured'): mixed
    {
        $profiler = self::getInstance();
        $profiler->time()->mark("{$label}_start");

        $startMem = memory_get_usage(true);
        $result   = $callback();
        $endMem   = memory_get_usage(true);

        $profiler->time()->mark("{$label}_end");
        $profiler->addData("{$label}_mem_used", $endMem - $startMem);

        return $result;
    }

    private function __clone(): void {}
    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }
}
