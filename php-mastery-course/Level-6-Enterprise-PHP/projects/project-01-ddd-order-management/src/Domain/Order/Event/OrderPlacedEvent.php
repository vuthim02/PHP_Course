<?php

declare(strict_types=1);

namespace DDD\Domain\Order\Event;

use DDD\Domain\Shared\DomainEventInterface;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Shared\ValueObjects\Money;

/**
 * OrderPlacedEvent
 *
 * Fired when an order is successfully placed.
 * Other bounded contexts (Payment, Shipping) react to this event.
 */
final class OrderPlacedEvent implements DomainEventInterface
{
    private float $occurredOn;

    public function __construct(
        private OrderId $orderId,
        private Money $total,
        private string $customerId
    ) {
        $this->occurredOn = microtime(true);
    }

    public function orderId(): OrderId { return $this->orderId; }
    public function total(): Money { return $this->total; }
    public function customerId(): string { return $this->customerId; }
    public function occurredOn(): float { return $this->occurredOn; }
}
