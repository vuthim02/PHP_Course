<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Category
{
    /** All categories with their product counts (for the admin panel). */
    public static function withCounts(): array
    {
        return db()->query(
            "SELECT c.id, c.name, c.slug, c.image_url, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY c.name"
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(string $name, string $slug, ?string $imageUrl = null): void
    {
        $stmt = db()->prepare(
            "INSERT INTO categories (name, slug, image_url) VALUES (:name, :slug, :img)"
        );
        $stmt->execute([
            ":name" => $name,
            ":slug" => $slug,
            ":img"  => $imageUrl,
        ]);
    }

    /** Deletes a category. Products in it are cascade-deleted (schema FK). */
    public static function delete(int $id): void
    {
        $stmt = db()->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
