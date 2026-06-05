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
        return $this->db->fetch("SELECT id, name, email, created_at FROM users WHERE id = ?", [$id]);
    }

    public function findAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM users");
        return (int) ($result['count'] ?? 0);
    }

    public function getPosts(int $userId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
    }

    public function getPostCount(int $userId): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM posts WHERE author_id = ?", [$userId]);
        return (int) ($result['count'] ?? 0);
    }

    public function getCommentCount(int $userId): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM comments WHERE user_id = ?", [$userId]);
        return (int) ($result['count'] ?? 0);
    }
}
