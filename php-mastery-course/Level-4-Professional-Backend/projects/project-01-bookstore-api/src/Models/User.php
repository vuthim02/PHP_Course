<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT id, name, email, role, created_at, updated_at FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function create(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['role'] = $data['role'] ?? 'user';
        $data['created_at'] = time();
        $data['updated_at'] = time();

        return $this->db->insert('users', $data);
    }

    public function update(int $id, array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        $data['updated_at'] = time();

        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('users', 'id = ?', [$id]);
    }

    public function findAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM users");
        return (int) ($result['count'] ?? 0);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
