<?php

declare(strict_types=1);

namespace DebuggerProfiler\collectors;

class QueryCollector
{
    private array $queries = [];
    private ?\PDO $pdo = null;

    public function setPdo(?\PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    public function log(string $sql, array $params = [], ?float $duration = null): void
    {
        $entry = [
            'sql'      => $sql,
            'params'   => $params,
            'duration' => $duration ?? 0,
            'time'     => microtime(true),
            'explain'  => null,
        ];

        if ($this->pdo !== null && preg_match('/^\s*SELECT\s/i', $sql)) {
            $entry['explain'] = $this->explain($sql);
        }

        $this->queries[] = $entry;
    }

    private function explain(string $sql): ?array
    {
        try {
            $stmt = $this->pdo->query("EXPLAIN $sql");
            return $stmt?->fetchAll(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function totalDuration(): float
    {
        return array_sum(array_column($this->queries, 'duration'));
    }

    public function count(): int
    {
        return count($this->queries);
    }

    public function reset(): void
    {
        $this->queries = [];
    }
}
