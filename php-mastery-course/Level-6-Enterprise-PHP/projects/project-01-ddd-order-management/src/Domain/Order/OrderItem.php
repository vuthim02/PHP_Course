<?php

declare(strict_types=1);

namespace DDD\Domain\Order;

use DDD\Domain\Shared\ValueObjects\Money;

/**
 * OrderItem Entity (part of Order aggregate)
 *
 * Exists only within an Order aggregate. Has local identity (productId, sku).
 */
final class OrderItem
{
    public function __construct(
        private string $productId,
        private string $productName,
        private Money $unitPrice,
        private int $quantity
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
    }

    public function productId(): string { return $this->productId; }
    public function productName(): string { return $this->productName; }
    public function unitPrice(): Money { return $this->unitPrice; }
    public function quantity(): int { return $this->quantity; }

    public function total(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }

    public function increaseQuantity(int $by): self
    {
        if ($by <= 0) {
            throw new \InvalidArgumentException('Increase amount must be positive');
        }
        return new self(
            $this->productId,
            $this->productName,
            $this->unitPrice,
            $this->quantity + $by
        );
    }
}
