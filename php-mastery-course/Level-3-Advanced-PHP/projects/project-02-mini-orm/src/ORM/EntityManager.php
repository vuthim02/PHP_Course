<?php

declare(strict_types=1);

namespace MiniORM;

use MiniORM\Attributes\BelongsTo;
use MiniORM\Attributes\Column;
use MiniORM\Attributes\Id;
use MiniORM\Attributes\OneToMany;
use MiniORM\Attributes\Table;

class EntityManager
{
    private array $entityMap = [];
    private array $migrationQueries = [];

    public function __construct(
        private readonly Connection $connection
    ) {}

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function persist(object $entity): void
    {
        $class = get_class($entity);
        $this->entityMap[$class][] = $entity;
    }

    public function flush(): void
    {
        foreach ($this->entityMap as $class => $entities) {
            foreach ($entities as $entity) {
                $this->save($entity);
            }
        }
        $this->entityMap = [];
    }

    public function save(object $entity): void
    {
        $refClass = new \ReflectionClass($entity);
        $tableAttr = $refClass->getAttributes(Table::class)[0] ?? null;

        if ($tableAttr === null) {
            throw new \RuntimeException('Entity ' . $refClass->getName() . ' is missing #[Table] attribute');
        }

        $tableName = $tableAttr->newInstance()->name;
        $columns = [];
        $values  = [];
        $idCol   = null;
        $idVal   = null;

        foreach ($refClass->getProperties() as $prop) {
            $colAttr = $prop->getAttributes(Column::class)[0] ?? null;
            $isId = $prop->getAttributes(Id::class) !== [];

            if ($colAttr === null) {
                continue;
            }

            $colName = $colAttr->newInstance()->name;
            $prop->setAccessible(true);

            if ($isId) {
                try {
                    $idVal = $prop->getValue($entity);
                } catch (\Error) {
                    $idVal = null;
                }
                $idCol = $colName;
                continue;
            }

            $val = $prop->getValue($entity);

            if ($val !== null) {
                $columns[] = $colName;
                $values[]  = $val;
            }
        }

        $pdo = $this->connection->connect();

        if ($idVal !== null) {
            $setClause = implode(', ', array_map(fn(string $c) => "$c = ?", $columns));
            $sql = "UPDATE {$tableName} SET {$setClause} WHERE {$idCol} = ?";
            $values[] = $idVal;
        } else {
            $cols = implode(', ', $columns);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = "INSERT INTO {$tableName} ({$cols}) VALUES ({$placeholders})";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        if ($idVal === null) {
            $idProp = $this->findIdProperty($refClass);
            if ($idProp !== null) {
                $idProp->setAccessible(true);
                $idProp->setValue($entity, (int) $this->connection->lastInsertId());
            }
        }
    }

    public function find(string $class, int $id): ?object
    {
        $meta = $this->getMetadata($class);
        $table = $meta['table'];
        $idCol = $meta['id_column'];

        $qb = new QueryBuilder($this->connection);
        $row = $qb->table($table)->where($idCol, '=', $id)->first();

        if ($row === null) {
            return null;
        }

        return $this->hydrate($class, $row);
    }

    public function findAll(string $class): array
    {
        $meta = $this->getMetadata($class);
        $qb = new QueryBuilder($this->connection);
        $rows = $qb->table($meta['table'])->get();

        return array_map(fn(array $row) => $this->hydrate($class, $row), $rows);
    }

    public function repository(string $class): Repository
    {
        return new Repository($class, $this);
    }

    public function createMigration(string $class): string
    {
        $refClass = new \ReflectionClass($class);
        $tableAttr = $refClass->getAttributes(Table::class)[0]?->newInstance();

        if ($tableAttr === null) {
            throw new \RuntimeException("Missing #[Table] attribute on $class");
        }

        $columns = [];
        foreach ($refClass->getProperties() as $prop) {
            $colAttrs = $prop->getAttributes(Column::class);
            $colAttr = $colAttrs !== [] ? $colAttrs[0]->newInstance() : null;
            $isId    = $prop->getAttributes(Id::class) !== [];

            if ($colAttr === null) {
                continue;
            }

            $type = match ($colAttr->type) {
                'int', 'integer' => 'INTEGER',
                'float', 'double' => 'FLOAT',
                'string'         => 'VARCHAR(255)',
                'text'           => 'TEXT',
                'bool', 'boolean' => 'BOOLEAN',
                'datetime'       => 'DATETIME',
                'array', 'json'  => 'JSON',
                default          => strtoupper($colAttr->type),
            };

            if ($isId) {
                $columns[] = "  {$colAttr->name} INTEGER PRIMARY KEY AUTOINCREMENT";
            } else {
                $nullable = $colAttr->nullable ? ' NULL' : ' NOT NULL';
                $default  = $colAttr->default !== null ? " DEFAULT '{$colAttr->default}'" : '';
                $columns[] = "  {$colAttr->name} {$type}{$nullable}{$default}";
            }
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$tableAttr->name} (\n" . implode(",\n", $columns) . "\n);";
        $this->migrationQueries[] = $sql;
        return $sql;
    }

    public function runMigrations(): array
    {
        $pdo = $this->connection->connect();
        $results = [];
        foreach ($this->migrationQueries as $sql) {
            try {
                $pdo->exec($sql);
                $results[] = "OK: $sql";
            } catch (\PDOException $e) {
                $results[] = "FAIL: $sql — {$e->getMessage()}";
            }
        }
        return $results;
    }

    public function getMetadata(string $class): array
    {
        $refClass = new \ReflectionClass($class);
        $tableAttr = $refClass->getAttributes(Table::class)[0]?->newInstance();

        if ($tableAttr === null) {
            throw new \RuntimeException("Missing #[Table] attribute on $class");
        }

        $columns = [];
        $idColumn = null;

        foreach ($refClass->getProperties() as $prop) {
            $colAttrs = $prop->getAttributes(Column::class);
            if ($colAttrs !== []) {
                $colAttr = $colAttrs[0]->newInstance();
                $columns[$prop->getName()] = $colAttr->name;
                if ($prop->getAttributes(Id::class) !== []) {
                    $idColumn = $colAttr->name;
                }
            }
        }

        return [
            'table'     => $tableAttr->name,
            'schema'    => $tableAttr->schema,
            'columns'   => $columns,
            'id_column' => $idColumn ?? 'id',
        ];
    }

    private function hydrate(string $class, array $data): object
    {
        $refClass = new \ReflectionClass($class);
        $entity   = $refClass->newInstanceWithoutConstructor();

        foreach ($refClass->getProperties() as $prop) {
            $colAttrs = $prop->getAttributes(Column::class);
            $colAttr = $colAttrs !== [] ? $colAttrs[0]->newInstance() : null;
            if ($colAttr === null) {
                continue;
            }

            $colName = $colAttr->name;
            if (!array_key_exists($colName, $data)) {
                continue;
            }

            $prop->setAccessible(true);
            $value = $data[$colName];

            if (in_array($colAttr->type, ['int', 'integer', 'float', 'double', 'string', 'bool', 'boolean'], true)) {
                settype($value, $colAttr->type);
            }
            $prop->setValue($entity, $value);
        }

        return $entity;
    }

    private function findIdProperty(\ReflectionClass $refClass): ?\ReflectionProperty
    {
        foreach ($refClass->getProperties() as $prop) {
            if ($prop->getAttributes(Id::class) !== []) {
                return $prop;
            }
        }
        return null;
    }
}
