<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Permission
{
    public ?int $id = null;
    public string $name;
    public ?string $description = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM permissions WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByName(string $name): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM permissions WHERE name = ?', [$name]);
        return $d ? self::hydrate($d) : null;
    }

    public static function all(): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM permissions ORDER BY name')
        );
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = ['name' => $this->name, 'description' => $this->description];
        if ($this->id) { $db->update('permissions', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('permissions', $d);
    }

    public function delete(): void { Database::getInstance()->delete('permissions', 'id = ?', [$this->id]); }

    public static function hydrate(object $d): self
    {
        $p = new self();
        $p->id = (int) $d->id;
        $p->name = $d->name;
        $p->description = $d->description ?? null;
        return $p;
    }
}
