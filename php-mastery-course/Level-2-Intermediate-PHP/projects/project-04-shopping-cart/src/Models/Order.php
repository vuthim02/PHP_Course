<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Order
{
    public ?int $id = null;
    public int $user_id;
    public float $total;
    public string $status = 'pending';
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM orders WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByUser(int $userId): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC', [$userId])
        );
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = ['user_id' => $this->user_id, 'total' => $this->total, 'status' => $this->status];
        if ($this->id) { $db->update('orders', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('orders', $d);
    }

    public function items(): array
    {
        return OrderItem::findByOrder($this->id);
    }

    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    private static function hydrate(object $d): self
    {
        $o = new self();
        $o->id = (int) $d->id; $o->user_id = (int) $d->user_id; $o->total = (float) $d->total; $o->status = $d->status; $o->created_at = $d->created_at ?? null;
        return $o;
    }
}
