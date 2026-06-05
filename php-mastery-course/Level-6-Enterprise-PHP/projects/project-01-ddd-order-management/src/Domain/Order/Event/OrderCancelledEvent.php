<?php

declare(strict_types=1);

namespace DDD\Domain\Order\Event;

use DDD\Domain\Shared\DomainEventInterface;
use DDD\Domain\Order\OrderId;

/**
 * OrderCancelledEvent
 *
 * Fired when an order is cancelled.
 * Triggers refund process, inventory restock, etc.
 */
final class OrderCancelledEvent implements DomainEventInterface
{
    private float $occurredOn;

    public function __construct(
        private OrderId $orderId,
        private string $reason
    ) {
        $this->occurredOn = microtime(true);
    }

    public function orderId(): OrderId { return $this->orderId; }
    public function reason(): string { return $this->reason; }
    public function occurredOn(): float { return $this->occurredOn; }
}
