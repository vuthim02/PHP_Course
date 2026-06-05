<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public function findAll(bool $withBookCount = false): array
    {
        if ($withBookCount) {
            return $this->db->fetchAll(
                "SELECT c.*, COUNT(b.id) as book_count
                 FROM categories c LEFT JOIN books b ON c.id = b.category_id
                 GROUP BY c.id ORDER BY c.name ASC"
            );
        }
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        return $this->db->insert('categories', $data);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('categories', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('categories', 'id = ?', [$id]);
    }
}
