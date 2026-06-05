<?php

declare(strict_types=1);

namespace MiniORM;

class Connection
{
    private ?\PDO $pdo = null;
    private ?InMemoryStorage $memory = null;

    public function __construct(
        private readonly string $dsn = '',
        private readonly string $username = '',
        private readonly string $password = '',
        private readonly array $options = []
    ) {}

    public function connect(): InMemoryStorage|\PDO
    {
        if ($this->pdo === null && $this->memory === null) {
            if ($this->dsn !== '' && \PDO::getAvailableDrivers() !== []) {
                try {
                    $this->pdo = new \PDO(
                        $this->dsn,
                        $this->username,
                        $this->password,
                        array_replace([
                            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                            \PDO::ATTR_EMULATE_PREPARES   => false,
                        ], $this->options)
                    );
                    return $this->pdo;
                } catch (\PDOException) {
                }
            }

            $this->memory = new InMemoryStorage();
        }

        return $this->memory ??= new InMemoryStorage();
    }

    public function disconnect(): void
    {
        $this->pdo = null;
        $this->memory = null;
    }

    public function lastInsertId(): string
    {
        $conn = $this->connect();
        if ($conn instanceof InMemoryStorage) {
            return (string) $conn->lastInsertId;
        }
        return $conn->lastInsertId();
    }
}

class InMemoryStorage
{
    public int $lastInsertId = 0;
    private array $tables = [];

    public function exec(string $sql): int
    {
        if (preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?(\w+)/i', $sql, $m)) {
            $table = $m[1];
            if (!isset($this->tables[$table])) {
                $this->tables[$table] = [];
            }
            return 0;
        }
        return 0;
    }

    public function prepare(string $sql): InMemoryStatement
    {
        return new InMemoryStatement($sql, $this);
    }

    public function query(string $sql): InMemoryStatement
    {
        return $this->prepare($sql);
    }

    public function insert(string $table, array $data): int
    {
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = [];
        }
        $this->lastInsertId++;
        $data['id'] = $this->lastInsertId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->tables[$table][] = $data;
        return $this->lastInsertId;
    }

    public function update(string $table, array $data, string $idCol, int $id): void
    {
        foreach ($this->tables[$table] ?? [] as $i => $row) {
            if (($row[$idCol] ?? null) === $id) {
                $this->tables[$table][$i] = array_merge($row, $data);
                return;
            }
        }
    }

    public function select(string $table, array $where = [], array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $rows = $this->tables[$table] ?? [];

        if ($where !== []) {
            $rows = array_filter($rows, function (array $row) use ($where): bool {
                foreach ($where as $col => $val) {
                    if (!array_key_exists($col, $row) || (string) $row[$col] !== (string) $val) {
                        return false;
                    }
                }
                return true;
            });
        }

        if ($orderBy !== []) {
            $col = array_key_first($orderBy);
            $dir = strtoupper($orderBy[$col]) === 'DESC' ? -1 : 1;
            usort($rows, function (array $a, array $b) use ($col, $dir): int {
                $cmp = ($a[$col] ?? '') <=> ($b[$col] ?? '');
                return $dir === -1 ? -$cmp : $cmp;
            });
        }

        if ($offset !== null) {
            $rows = array_slice($rows, $offset);
        }

        if ($limit !== null) {
            $rows = array_slice($rows, 0, $limit);
        }

        return array_values($rows);
    }

    public function delete(string $table, string $idCol, int $id): void
    {
        foreach ($this->tables[$table] ?? [] as $i => $row) {
            if (($row[$idCol] ?? null) === $id) {
                array_splice($this->tables[$table], $i, 1);
                return;
            }
        }
    }

    public function countAll(string $table, array $where = []): int
    {
        return count($this->select($table, $where));
    }
}

class InMemoryStatement
{
    private array $boundParams = [];

    public function __construct(
        private readonly string $sql,
        private readonly InMemoryStorage $storage
    ) {}

public function execute(?array $params = null): bool
    {
        if ($params !== null) {
            $this->boundParams = $params;
        }

        $sql = $this->sql;

        if (preg_match('/^\s*INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\(([^)]+)\)/is', $sql, $m)) {
            $this->runInsert($m[1], $m[2]);
            return true;
        }

        if (preg_match('/^\s*UPDATE\s+(\w+)/is', $sql, $m)) {
            $this->runUpdate($m[1]);
            return true;
        }

        if (preg_match('/^\s*DELETE\s+FROM\s+(\w+)/is', $sql, $m)) {
            $this->runDelete($m[1]);
            return true;
        }

        return true;
    }

    private function runInsert(string $table, string $colsStr): void
    {
        $cols = array_map('trim', explode(',', $colsStr));
        $data = [];
        foreach ($cols as $i => $col) {
            $data[$col] = $this->boundParams[$i] ?? null;
        }
        $this->storage->insert($table, $data);
    }

    private function runUpdate(string $table): void
    {
        if (preg_match('/SET\s+(.+?)\s+WHERE\s+(.+?)$/is', $this->sql, $m)) {
            $setPart = $m[1];
            $wherePart = $m[2];

            preg_match_all('/(\w+)\s*=\s*\?/i', $setPart, $setMatches);
            $data = [];
            $paramIdx = 0;
            foreach ($setMatches[1] as $col) {
                $data[trim($col)] = $this->boundParams[$paramIdx++] ?? null;
            }

            if (preg_match('/^(\w+)\s*=\s*\?$/i', trim($wherePart), $wm)) {
                $idCol = $wm[1];
                $idVal = $this->boundParams[$paramIdx] ?? null;
                if ($idVal !== null) {
                    $this->storage->update($table, $data, $idCol, (int) $idVal);
                }
            }
        }
    }

    private function runDelete(string $table): void
    {
        if (preg_match('/WHERE\s+(.+?)$/is', $this->sql, $m)) {
            if (preg_match('/^(\w+)\s*=\s*\?$/i', trim($m[1]), $wm)) {
                $idCol = $wm[1];
                $idVal = (int) ($this->boundParams[0] ?? 0);
                $this->storage->delete($table, $idCol, $idVal);
            }
        }
    }

    public function fetch(int $mode = \PDO::FETCH_ASSOC): mixed
    {
        $rows = $this->fetchAll($mode);
        return $rows[0] ?? false;
    }

    public function fetchAll(int $mode = \PDO::FETCH_ASSOC): array
    {
        $sql = $this->sql;

        if (preg_match('/^\s*INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\(([^)]+)\)/is', $sql, $m)) {
            $table = $m[1];
            $cols = array_map('trim', explode(',', $m[2]));
            $data = [];
            foreach ($cols as $i => $col) {
                $data[$col] = $this->boundParams[$i] ?? null;
            }
            $this->storage->insert($table, $data);
            return [];
        }

        if (preg_match('/^\s*UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE\s+(.+?)$/is', $sql, $m)) {
            $table = $m[1];
            $setPart = $m[2];
            $wherePart = $m[3];

            preg_match_all('/(\w+)\s*=\s*\?/i', $setPart, $setMatches);
            $data = [];
            $paramIdx = 0;
            foreach ($setMatches[1] as $col) {
                $data[trim($col)] = $this->boundParams[$paramIdx++] ?? null;
            }

            if (preg_match('/^(\w+)\s*=\s*\?$/i', trim($wherePart), $wm)) {
                $idCol = $wm[1];
                $idVal = $this->boundParams[$paramIdx] ?? null;
                if ($idVal !== null) {
                    $this->storage->update($table, $data, $idCol, (int) $idVal);
                }
            }

            return [];
        }

        if (preg_match('/^\s*DELETE\s+FROM\s+(\w+)\s+WHERE\s+(.+?)$/is', $sql, $m)) {
            $table = $m[1];
            if (preg_match('/^(\w+)\s*=\s*\?$/i', trim($m[2]), $wm)) {
                $idCol = $wm[1];
                $idVal = (int) ($this->boundParams[0] ?? 0);
                $this->storage->delete($table, $idCol, $idVal);
            }
            return [];
        }

        if (preg_match('/SELECT\s+COUNT\s*\(\s*\*\s*\)\s+as\s+count\s+FROM\s+(\w+)/is', $sql, $m)) {
            $table = $m[1];
            $where = $this->parseWhere();
            return [['count' => $this->storage->countAll($table, $where)]];
        }

        if (preg_match('/SELECT\s+(.*?)\s+FROM\s+(\w+)/is', $sql, $m)) {
            $table = $m[2];
            $where = $this->parseWhere();
            $orderBy = $this->parseOrderBy();
            $limit = $this->parseLimit();
            $offset = $this->parseOffset();
            return $this->storage->select($table, $where, $orderBy, $limit, $offset);
        }

        return [];
    }

    private function parseWhere(): array
    {
        $where = [];
        if (preg_match('/WHERE\s+(.+?)(?:ORDER BY|LIMIT|OFFSET|$)/is', $this->sql, $m)) {
            preg_match_all('/(\w+)\s*=\s*\?/i', $m[1], $matches);
            $i = 0;
            foreach ($matches[1] as $col) {
                $where[trim($col)] = $this->boundParams[$i++] ?? null;
            }
        }
        return $where;
    }

    private function parseOrderBy(): array
    {
        if (preg_match('/ORDER BY\s+(\w+)\s+(ASC|DESC)/is', $this->sql, $m)) {
            return [trim($m[1]) => strtoupper($m[2])];
        }
        return [];
    }

    private function parseLimit(): ?int
    {
        if (preg_match('/LIMIT\s+(\d+)/is', $this->sql, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function parseOffset(): ?int
    {
        if (preg_match('/OFFSET\s+(\d+)/is', $this->sql, $m)) {
            return (int) $m[1];
        }
        return null;
    }
}
