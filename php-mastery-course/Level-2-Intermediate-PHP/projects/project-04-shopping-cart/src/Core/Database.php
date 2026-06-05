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
        $c = require __DIR__ . '/../../config/database.php';
        $dsn = "{$c['driver']}:host={$c['host']};port={$c['port']};dbname={$c['dbname']};charset={$c['charset']}";
        try {
            $this->pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('DB failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function query(string $sql, array $p = []): \PDOStatement
    {
        $s = $this->pdo->prepare($sql); $s->execute($p); return $s;
    }

    public function fetch(string $sql, array $p = []): ?object
    {
        $r = $this->query($sql, $p)->fetch(); return $r ?: null;
    }

    public function fetchAll(string $sql, array $p = []): array
    {
        return $this->query($sql, $p)->fetchAll();
    }

    public function insert(string $t, array $d): int
    {
        $c = implode(', ', array_keys($d));
        $ph = implode(', ', array_fill(0, count($d), '?'));
        $this->query("INSERT INTO {$t} ({$c}) VALUES ({$ph})", array_values($d));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $t, array $d, string $w, array $wp = []): int
    {
        $s = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($d)));
        return $this->query("UPDATE {$t} SET {$s} WHERE {$w}", [...array_values($d), ...$wp])->rowCount();
    }

    public function delete(string $t, string $w, array $p = []): int
    {
        return $this->query("DELETE FROM {$t} WHERE {$w}", $p)->rowCount();
    }
}
