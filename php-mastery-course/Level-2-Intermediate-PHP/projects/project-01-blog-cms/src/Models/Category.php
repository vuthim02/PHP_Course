<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    public ?int $id = null;
    public string $name;
    public string $slug;
    public ?string $description = null;

    public static function find(int $id): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM categories WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
        return $data ? self::hydrate($data) : null;
    }

    public static function all(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            'SELECT c.*, COUNT(p.id) as post_count
             FROM categories c
             LEFT JOIN posts p ON c.id = p.category_id
             GROUP BY c.id
             ORDER BY c.name ASC'
        );
        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();

        if ($this->id) {
            $db->update('categories', [
                'name'        => $this->name,
                'slug'        => $this->slug,
                'description' => $this->description,
            ], 'id = ?', [$this->id]);
            return $this->id;
        }

        return $db->insert('categories', [
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
        ]);
    }

    public function delete(): void
    {
        $db = Database::getInstance();
        $db->delete('categories', 'id = ?', [$this->id]);
    }

    public function posts(string $status = 'published'): array
    {
        return Post::findByCategory($this->id, $status);
    }

    public static function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $db = Database::getInstance();
        $count = $db->fetch('SELECT COUNT(*) as count FROM categories WHERE slug LIKE ?', ["{$slug}%"]);
        return $count->count > 0 ? "{$slug}-{$count->count}" : $slug;
    }

    private static function hydrate(object $data): self
    {
        $category = new self();
        $category->id = (int) $data->id;
        $category->name = $data->name;
        $category->slug = $data->slug;
        $category->description = $data->description ?? null;
        return $category;
    }
}
