<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Comment
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT c.*, u.name as user_name
             FROM comments c JOIN users u ON c.user_id = u.id
             WHERE c.id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        return $this->db->insert('comments', $data);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('comments', 'id = ?', [$id]);
    }
}
