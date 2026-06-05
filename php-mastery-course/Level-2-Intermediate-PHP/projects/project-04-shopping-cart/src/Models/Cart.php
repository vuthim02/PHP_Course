<?php

declare(strict_types=1);

namespace App\Models;

class Cart
{
    public array $items = [];

    public function __construct()
    {
        $this->load();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        if (isset($this->items[$productId])) {
            $this->items[$productId] += $quantity;
        } else {
            $this->items[$productId] = $quantity;
        }
        $this->save();
    }

    public function update(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }
        $this->items[$productId] = $quantity;
        $this->save();
    }

    public function remove(int $productId): void
    {
        unset($this->items[$productId]);
        $this->save();
    }

    public function clear(): void
    {
        $this->items = [];
        $this->save();
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function count(): int
    {
        return array_sum($this->items);
    }

    public function getItems(): array
    {
        $result = [];
        foreach ($this->items as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $result[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            }
        }
        return $result;
    }

    public function total(): float
    {
        $sum = 0.0;
        foreach ($this->getItems() as $item) {
            $sum += $item['subtotal'];
        }
        return $sum;
    }

    private function load(): void
    {
        $this->items = $_SESSION['cart'] ?? [];
    }

    private function save(): void
    {
        $_SESSION['cart'] = $this->items;
    }
}
