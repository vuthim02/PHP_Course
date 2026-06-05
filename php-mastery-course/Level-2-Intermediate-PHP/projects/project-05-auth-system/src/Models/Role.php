<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Role
{
    public ?int $id = null;
    public string $name;
    public ?string $description = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM roles WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByName(string $name): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM roles WHERE name = ?', [$name]);
        return $d ? self::hydrate($d) : null;
    }

    public static function all(): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM roles ORDER BY name')
        );
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = ['name' => $this->name, 'description' => $this->description];
        if ($this->id) { $db->update('roles', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('roles', $d);
    }

    public function delete(): void { Database::getInstance()->delete('roles', 'id = ?', [$this->id]); }

    public function permissions(): array
    {
        $rows = Database::getInstance()->fetchAll(
            'SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?', [$this->id]
        );
        return array_map(fn($r) => Permission::hydrate($r), $rows);
    }

    public function assignPermission(int $permissionId): void
    {
        Database::getInstance()->insert('role_permissions', ['role_id' => $this->id, 'permission_id' => $permissionId]);
    }

    public function removePermission(int $permissionId): void
    {
        Database::getInstance()->delete('role_permissions', 'role_id = ? AND permission_id = ?', [$this->id, $permissionId]);
    }

    public static function hydrate(object $d): self
    {
        $r = new self();
        $r->id = (int) $d->id;
        $r->name = $d->name;
        $r->description = $d->description ?? null;
        return $r;
    }
}
