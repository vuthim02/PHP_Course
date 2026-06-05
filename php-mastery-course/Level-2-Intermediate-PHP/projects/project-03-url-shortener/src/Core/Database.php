<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('DB connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?object
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): int
    {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO {$table} ({$cols}) VALUES ({$phs})", array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $wp = []): int
    {
        $sets = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $stmt = $this->query("UPDATE {$table} SET {$sets} WHERE {$where}", [...array_values($data), ...$wp]);
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $p = []): int
    {
        return $this->query("DELETE FROM {$table} WHERE {$where}", $p)->rowCount();
    }
}
