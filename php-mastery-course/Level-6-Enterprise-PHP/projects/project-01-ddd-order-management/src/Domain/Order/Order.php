<?php

declare(strict_types=1);

namespace DDD\Domain\Order;

use DDD\Domain\Shared\ValueObjects\Address;
use DDD\Domain\Shared\ValueObjects\Money;
use DDD\Domain\Order\Event\OrderPlacedEvent;
use DDD\Domain\Order\Event\OrderCancelledEvent;

/**
 * Order Aggregate Root
 *
 * The Order is the aggregate root in the Ordering bounded context.
 * It encapsulates all order-related invariants:
 *   - Total must match sum of line items
 *   - Status transitions are validated
 *   - Items cannot be modified after confirmation
 *   - Domain events are recorded for side effects
 *
 * Aggregate = consistency boundary. All changes go through the aggregate root.
 */
final class Order
{
    /** @var DomainEventInterface[] */
    private array $recordedEvents = [];

    /** @var OrderItem[] */
    private array $items;

    public function __construct(
        private OrderId $id,
        private string $customerId,
        private Address $shippingAddress,
        private Address $billingAddress,
        private OrderStatus $status,
        array $items = []
    ) {
        $this->items = $items;
    }

    public static function place(
        OrderId $id,
        string $customerId,
        Address $shippingAddress,
        Address $billingAddress,
        array $items
    ): self {
        if (empty($items)) {
            throw new \DomainException('Cannot place order with no items');
        }

        $order = new self(
            $id,
            $customerId,
            $shippingAddress,
            $billingAddress,
            OrderStatus::pending(),
            $items
        );

        // Record domain event for side effects (payment, notification, etc.)
        $order->recordEvent(new OrderPlacedEvent($id, $order->total(), $customerId));

        return $order;
    }

    public function cancel(string $reason): void
    {
        // Business rule: only cancellable if not yet delivered
        if ($this->status->value() === OrderStatus::DELIVERED) {
            throw new \DomainException('Cannot cancel a delivered order');
        }

        $this->status = $this->status->transitionTo(OrderStatus::cancelled());

        $this->recordEvent(new OrderCancelledEvent($this->id, $reason));
    }

    public function confirm(): void
    {
        $this->status = $this->status->transitionTo(OrderStatus::confirmed());
    }

    public function markShipped(): void
    {
        $this->status = $this->status->transitionTo(OrderStatus::shipped());
    }

    public function markDelivered(): void
    {
        $this->status = $this->status->transitionTo(OrderStatus::delivered());
    }

    public function total(): Money
    {
        $total = new Money(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->total());
        }
        return $total;
    }

    // --- Event recording / retrieval ---

    public function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function recordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearEvents(): void
    {
        $this->recordedEvents = [];
    }

    // --- Getters ---

    public function id(): OrderId { return $this->id; }
    public function customerId(): string { return $this->customerId; }
    public function shippingAddress(): Address { return $this->shippingAddress; }
    public function billingAddress(): Address { return $this->billingAddress; }
    public function status(): OrderStatus { return $this->status; }
    public function items(): array { return $this->items; }
}
