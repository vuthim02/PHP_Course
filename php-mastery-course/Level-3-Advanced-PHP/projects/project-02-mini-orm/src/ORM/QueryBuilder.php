<?php

declare(strict_types=1);

namespace MiniORM;

class QueryBuilder
{
    private string $table;
    private array $selects = ['*'];
    private array $wheres = [];
    private array $params = [];
    private array $orderBys = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];

    public function __construct(private readonly Connection $connection) {}

    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function select(string ...$columns): static
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, string $operator = '=', mixed $value = null): static
    {
        $this->wheres[] = [$column, $operator, $value];
        if ($value !== null) {
            $this->params[] = $value;
        }
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->wheres[] = [$column, 'IN', "($placeholders)"];
        $this->params = array_merge($this->params, array_values($values));
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBys[] = "$column $direction";
        return $this;
    }

    public function limit(?int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(?int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): static
    {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->selects) . ' FROM ' . $this->table;

        foreach ($this->joins as $join) {
            $sql .= ' ' . $join;
        }

        if ($this->wheres !== []) {
            $conditions = array_map(
                fn(array $w) => $w[1] === 'IN'
                    ? "{$w[0]} {$w[1]} {$w[2]}"
                    : "{$w[0]} {$w[1]} ?",
                $this->wheres
            );
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($this->orderBys !== []) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBys);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    public function get(): array
    {
        $stmt = $this->connection->connect()->prepare($this->toSql());
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    public function count(): int
    {
        $this->selects = ['COUNT(*) as count'];
        $row = $this->first();
        return (int) ($row['count'] ?? 0);
    }
}
