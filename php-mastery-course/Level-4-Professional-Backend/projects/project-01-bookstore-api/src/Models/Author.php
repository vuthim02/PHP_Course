<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Author
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM authors WHERE id = ?", [$id]);
    }

    public function findAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $items = $this->db->fetchAll(
            "SELECT a.*, COUNT(b.id) as book_count
             FROM authors a LEFT JOIN books b ON a.id = b.author_id
             GROUP BY a.id ORDER BY a.name ASC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = $this->db->fetch("SELECT COUNT(*) as count FROM authors");

        return [
            'items' => $items,
            'total' => (int) ($total['count'] ?? 0),
        ];
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->db->insert('authors', $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = time();
        return $this->db->update('authors', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('authors', 'id = ?', [$id]);
    }
}
