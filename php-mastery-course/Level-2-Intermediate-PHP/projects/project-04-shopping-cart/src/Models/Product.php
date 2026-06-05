<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Product
{
    public ?int $id = null;
    public string $name;
    public string $slug;
    public string $description;
    public float $price;
    public int $stock = 0;
    public ?string $image_url = null;
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM products WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM products WHERE slug = ?', [$slug]);
        return $d ? self::hydrate($d) : null;
    }

    public static function all(): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM products ORDER BY created_at DESC')
        );
    }

    public static function paginate(int $page = 1, int $per = 12): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $per;
        $total = $db->fetch('SELECT COUNT(*) as c FROM products');
        $rows = $db->fetchAll('SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?', [$per, $offset]);
        return [
            'items' => array_map(fn($r) => self::hydrate($r), $rows),
            'total' => (int) $total->c,
            'page' => $page,
            'pages' => (int) ceil((int) $total->c / $per),
        ];
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = ['name' => $this->name, 'slug' => $this->slug, 'description' => $this->description, 'price' => $this->price, 'stock' => $this->stock, 'image_url' => $this->image_url];
        if ($this->id) { $db->update('products', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('products', $d);
    }

    public function isInStock(int $qty = 1): bool { return $this->stock >= $qty; }

    public function formattedPrice(): string { return number_format($this->price, 2); }

    private static function hydrate(object $d): self
    {
        $p = new self();
        $p->id = (int) $d->id; $p->name = $d->name; $p->slug = $d->slug; $p->description = $d->description;
        $p->price = (float) $d->price; $p->stock = (int) $d->stock; $p->image_url = $d->image_url ?? null; $p->created_at = $d->created_at ?? null;
        return $p;
    }
}
