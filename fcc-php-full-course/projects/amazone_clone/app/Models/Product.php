<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Product
{
    private const BASE_SELECT = "SELECT p.id, p.category_id, p.name, p.slug, p.description, p.price,
        p.deal_price, p.stock, p.image_url, p.rating_avg, p.rating_count, c.name AS category
        FROM products p
        JOIN categories c ON c.id = p.category_id";

    public static function featured(int $limit): array
    {
        $stmt = db()->prepare(self::BASE_SELECT . " ORDER BY p.rating_count DESC LIMIT :limit");
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function all(int $categoryId = 0): array
    {
        if ($categoryId > 0) {
            $stmt = db()->prepare(self::BASE_SELECT . " WHERE p.category_id = :cid ORDER BY p.name");
            $stmt->bindValue(":cid", $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }

        return db()->query(self::BASE_SELECT . " ORDER BY p.name")->fetchAll();
    }

    public static function search(string $term): array
    {
        $like = "%" . $term . "%";
        $stmt = db()->prepare(
            self::BASE_SELECT
            . " WHERE p.name LIKE :q OR p.description LIKE :q2"
            . " ORDER BY p.rating_count DESC"
        );
        $stmt->bindValue(":q", $like);
        $stmt->bindValue(":q2", $like);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = db()->prepare(self::BASE_SELECT . " WHERE p.slug = :slug LIMIT 1");
        $stmt->bindValue(":slug", $slug);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare(self::BASE_SELECT . " WHERE p.id = :id LIMIT 1");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Products currently on sale (deal_price lower than price), best discounts first. */
    public static function deals(int $categoryId = 0): array
    {
        $sql = self::BASE_SELECT
            . " WHERE p.deal_price IS NOT NULL AND p.deal_price < p.price";
        if ($categoryId > 0) {
            $sql .= " AND p.category_id = :cid";
        }
        $sql .= " ORDER BY (p.price - p.deal_price) DESC";

        $stmt = db()->prepare($sql);
        if ($categoryId > 0) {
            $stmt->bindValue(":cid", $categoryId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** The single biggest current deal, shown as "Deal of the Day". */
    public static function dealOfTheDay(): ?array
    {
        $stmt = db()->prepare(
            self::BASE_SELECT
            . " WHERE p.deal_price IS NOT NULL AND p.deal_price < p.price"
            . " ORDER BY (p.price - p.deal_price) DESC LIMIT 1"
        );
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function byCategorySlug(string $slug): array
    {
        $stmt = db()->prepare(self::BASE_SELECT . " WHERE c.slug = :slug ORDER BY p.price");
        $stmt->bindValue(":slug", $slug);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function categories(): array
    {
        return db()->query("SELECT id, name, slug, image_url FROM categories ORDER BY name")->fetchAll();
    }

    // ---- Admin helpers -------------------------------------------------------

    public static function count(): int
    {
        return (int) db()->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    /** Products with dangerously low stock, low first. */
    public static function lowStock(int $threshold = 10): array
    {
        $stmt = db()->prepare(
            self::BASE_SELECT . " WHERE p.stock <= :t ORDER BY p.stock ASC"
        );
        $stmt->bindValue(":t", $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Products + category name for the admin table (optionally filtered by name). */
    public static function adminAll(string $q = ""): array
    {
        $sql = self::BASE_SELECT;
        if ($q !== "") {
            $sql .= " WHERE p.name LIKE :q";
        }
        $sql .= " ORDER BY p.id DESC";

        $stmt = db()->prepare($sql);
        if ($q !== "") {
            $stmt->bindValue(":q", "%" . $q . "%");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM products WHERE slug = :slug";
        if ($ignoreId !== null) {
            $sql .= " AND id != :id";
        }
        $stmt = db()->prepare($sql);
        $stmt->bindValue(":slug", $slug);
        if ($ignoreId !== null) {
            $stmt->bindValue(":id", $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Inserts a product and returns its new id. */
    public static function createFrom(array $d): int
    {
        $stmt = db()->prepare(
            "INSERT INTO products (category_id, name, slug, description, price, deal_price, stock, image_url)
             VALUES (:cid, :name, :slug, :desc, :price, :deal, :stock, :img)"
        );
        $stmt->execute([
            ":cid"   => $d["category_id"],
            ":name"  => $d["name"],
            ":slug"  => $d["slug"],
            ":desc"  => $d["description"],
            ":price" => $d["price"],
            ":deal"  => $d["deal_price"],
            ":stock" => $d["stock"],
            ":img"   => $d["image_url"],
        ]);
        return (int) db()->lastInsertId();
    }

    public static function updateFrom(int $id, array $d): void
    {
        $stmt = db()->prepare(
            "UPDATE products
             SET category_id = :cid, name = :name, slug = :slug, description = :desc,
                 price = :price, deal_price = :deal, stock = :stock, image_url = :img
             WHERE id = :id"
        );
        $stmt->execute([
            ":cid"   => $d["category_id"],
            ":name"  => $d["name"],
            ":slug"  => $d["slug"],
            ":desc"  => $d["description"],
            ":price" => $d["price"],
            ":deal"  => $d["deal_price"],
            ":stock" => $d["stock"],
            ":img"   => $d["image_url"],
            ":id"    => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = db()->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** Copies a product (new name + unique slug, zero stock). */
    public static function duplicate(int $id): void
    {
        $p = self::findById($id);
        if ($p === null) {
            return;
        }
        $slug = $p["slug"] . "-copy";
        if (self::slugExists($slug)) {
            $slug .= "-" . bin2hex(random_bytes(2));
        }
        self::createFrom([
            "category_id" => (int) $p["category_id"],
            "name"        => $p["name"] . " (copy)",
            "slug"        => $slug,
            "description" => $p["description"],
            "price"       => (float) $p["price"],
            "deal_price"  => $p["deal_price"],
            "stock"       => 0,
            "image_url"   => $p["image_url"],
        ]);
    }
}
