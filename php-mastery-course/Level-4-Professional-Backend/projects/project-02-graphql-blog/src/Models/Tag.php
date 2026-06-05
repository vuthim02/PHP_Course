<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Tag
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM tags WHERE id = ?", [$id]);
    }

    public function findAll(): array
    {
        return $this->db->fetchAll("SELECT t.*, COUNT(pt.post_id) as post_count FROM tags t LEFT JOIN post_tags pt ON t.id = pt.tag_id GROUP BY t.id ORDER BY t.name ASC");
    }

    public function findOrCreate(string $name): int
    {
        $existing = $this->db->fetch("SELECT id FROM tags WHERE name = ?", [$name]);
        if ($existing) {
            return (int) $existing['id'];
        }
        return $this->db->insert('tags', ['name' => $name, 'created_at' => time()]);
    }
}
