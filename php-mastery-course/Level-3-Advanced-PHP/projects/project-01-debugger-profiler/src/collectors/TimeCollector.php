<?php

declare(strict_types=1);

namespace DebuggerProfiler\collectors;

class TimeCollector
{
    private array $marks = [];
    private float $start;

    public function __construct()
    {
        $this->start = microtime(true);
    }

    public function mark(string $name): void
    {
        $this->marks[] = [
            'name' => $name,
            'time' => microtime(true),
            'memory' => memory_get_usage(true),
        ];
    }

    public function elapsed(?string $from = null): float
    {
        $base = $from !== null
            ? ($this->marks[$from]['time'] ?? $this->start)
            : $this->start;

        return microtime(true) - $base;
    }

    public function getMarks(): array
    {
        return $this->marks;
    }

    public function reset(): void
    {
        $this->marks = [];
        $this->start = microtime(true);
    }
}
