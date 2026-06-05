<?php

declare(strict_types=1);

namespace MiniORM;

class Repository
{
    private readonly string $table;
    private readonly string $idColumn;

    public function __construct(
        private readonly string $entityClass,
        private readonly EntityManager $em
    ) {
        $meta = $em->getMetadata($entityClass);
        $this->table    = $meta['table'];
        $this->idColumn = $meta['id_column'];
    }

    public function find(int $id): ?object
    {
        return $this->em->find($this->entityClass, $id);
    }

    public function findAll(): array
    {
        return $this->em->findAll($this->entityClass);
    }

    public function findBy(array $criteria, array $orderBy = [], ?int $limit = null): array
    {
        $qb = new QueryBuilder($this->em->getConnection());
        $qb->table($this->table);

        foreach ($criteria as $col => $val) {
            $qb->where($col, '=', $val);
        }

        foreach ($orderBy as $col => $dir) {
            $qb->orderBy($col, $dir);
        }

        if ($limit !== null) {
            $qb->limit($limit);
        }

        $rows = $qb->get();
        return array_map(fn(array $row) => $this->em->find($this->entityClass, $row[$this->idColumn]), $rows);
    }

    public function findOneBy(array $criteria): ?object
    {
        $results = $this->findBy($criteria, [], 1);
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $qb = new QueryBuilder($this->em->getConnection());
        return $qb->table($this->table)->count();
    }

    public function delete(int $id): void
    {
        $pdo = $this->em->getConnection()->connect();
        $stmt = $pdo->prepare("DELETE FROM {$this->table} WHERE {$this->idColumn} = ?");
        $stmt->execute([$id]);
    }

    public function query(): QueryBuilder
    {
        return (new QueryBuilder($this->em->getConnection()))->table($this->table);
    }
}
