<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class OrderItem
{
    public ?int $id = null;
    public int $order_id;
    public int $product_id;
    public string $product_name;
    public float $price;
    public int $quantity;
    public float $subtotal;

    public static function findByOrder(int $orderId): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM order_items WHERE order_id = ?', [$orderId])
        );
    }

    public function save(): int
    {
        return Database::getInstance()->insert('order_items', [
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ]);
    }

    private static function hydrate(object $d): self
    {
        $i = new self();
        $i->id = (int) $d->id; $i->order_id = (int) $d->order_id; $i->product_id = (int) $d->product_id;
        $i->product_name = $d->product_name; $i->price = (float) $d->price; $i->quantity = (int) $d->quantity; $i->subtotal = (float) $d->subtotal;
        return $i;
    }
}
