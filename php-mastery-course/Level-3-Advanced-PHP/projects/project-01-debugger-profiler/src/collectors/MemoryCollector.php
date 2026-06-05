<?php

declare(strict_types=1);

namespace DebuggerProfiler\collectors;

class MemoryCollector
{
    private array $snapshots = [];

    public function snapshot(string $label = ''): array
    {
        $usage = memory_get_usage(true);
        $peak  = memory_get_peak_usage(true);

        $this->snapshots[] = [
            'label' => $label,
            'usage' => $usage,
            'peak'  => $peak,
            'time'  => microtime(true),
        ];

        return ['usage' => $usage, 'peak' => $peak];
    }

    public function getSnapshots(): array
    {
        return $this->snapshots;
    }

    public function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function peakUsage(): int
    {
        return memory_get_peak_usage(true);
    }
}
