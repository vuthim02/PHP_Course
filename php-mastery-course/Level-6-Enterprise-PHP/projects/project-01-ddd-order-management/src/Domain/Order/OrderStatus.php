<?php

declare(strict_types=1);

namespace DDD\Domain\Order;

/**
 * OrderStatus Value Object (Enum-like)
 *
 * Represents the lifecycle state of an order.
 * PHP 8.1+ would use a backed enum, but this demonstrates value object pattern.
 */
final class OrderStatus
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const PROCESSING = 'processing';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';

    private const VALID_TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::PROCESSING, self::CANCELLED],
        self::PROCESSING => [self::SHIPPED, self::CANCELLED],
        self::SHIPPED => [self::DELIVERED],
        self::DELIVERED => [],
        self::CANCELLED => [self::REFUNDED],
        self::REFUNDED => [],
    ];

    public function __construct(private string $status)
    {
        if (!in_array($status, self::validStatuses(), true)) {
            throw new \InvalidArgumentException("Invalid order status: {$status}");
        }
    }

    public static function pending(): self { return new self(self::PENDING); }

    public function value(): string { return $this->status; }

    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus->value(), self::VALID_TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(self $newStatus): self
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \DomainException(
                "Cannot transition from '{$this->status}' to '{$newStatus->value()}'"
            );
        }
        return $newStatus;
    }

    public function equals(self $other): bool
    {
        return $this->status === $other->status;
    }

    public static function validStatuses(): array
    {
        return [
            self::PENDING, self::CONFIRMED, self::PROCESSING,
            self::SHIPPED, self::DELIVERED, self::CANCELLED, self::REFUNDED,
        ];
    }

    public function __toString(): string
    {
        return $this->status;
    }
}
