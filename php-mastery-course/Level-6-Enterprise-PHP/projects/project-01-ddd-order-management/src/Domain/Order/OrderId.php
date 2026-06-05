<?php

declare(strict_types=1);

namespace DDD\Domain\Order;

/**
 * OrderId Value Object
 *
 * Strongly-typed identifier for Order aggregate root.
 * Using UUIDs for distributed system compatibility.
 */
final class OrderId
{
    public function __construct(private string $id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Order ID cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
