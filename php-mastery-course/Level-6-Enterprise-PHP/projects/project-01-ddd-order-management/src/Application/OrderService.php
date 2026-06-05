<?php

declare(strict_types=1);

namespace DDD\Application;

use DDD\Domain\Order\Order;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Order\OrderItem;
use DDD\Domain\Order\OrderRepositoryInterface;
use DDD\Domain\Shared\ValueObjects\Address;
use DDD\Domain\Shared\ValueObjects\Money;
use DDD\Infrastructure\Event\InMemoryEventBus;

/**
 * OrderService
 *
 * Application service in the Ordering bounded context.
 * Encapsulates use cases: placeOrder, cancelOrder, getOrder.
 * Application services are thin — they orchestrate, coordinate, and delegate
 * to domain objects. Business logic lives in the domain layer.
 */
final class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InMemoryEventBus $eventBus
    ) {
    }

    /**
     * Place a new order.
     * Creates the aggregate, saves it, and dispatches domain events.
     */
    public function placeOrder(
        string $customerId,
        Address $shippingAddress,
        Address $billingAddress,
        array $itemsData
    ): Order {
        $orderId = OrderId::generate();

        $items = [];
        foreach ($itemsData as $data) {
            $items[] = new OrderItem(
                $data['productId'],
                $data['productName'],
                Money::fromFloat($data['price'], $data['currency'] ?? 'USD'),
                $data['quantity']
            );
        }

        // Factory method on the aggregate root ensures valid state
        $order = Order::place(
            $orderId,
            $customerId,
            $shippingAddress,
            $billingAddress,
            $items
        );

        $this->orderRepository->save($order);

        // Dispatch domain events to trigger side effects (payment, shipping, etc.)
        $this->eventBus->dispatchAll($order->recordedEvents());
        $order->clearEvents();

        echo "[Order] Order {$orderId} placed successfully\n";

        return $order;
    }

    /**
     * Cancel an existing order.
     */
    public function cancelOrder(OrderId $orderId, string $reason): Order
    {
        $order = $this->orderRepository->getById($orderId);
        if ($order === null) {
            throw new \RuntimeException("Order {$orderId} not found");
        }

        $order->cancel($reason);

        $this->orderRepository->save($order);
        $this->eventBus->dispatchAll($order->recordedEvents());
        $order->clearEvents();

        echo "[Order] Order {$orderId} cancelled: {$reason}\n";

        return $order;
    }

    /**
     * Retrieve an order by ID.
     */
    public function getOrder(OrderId $orderId): ?Order
    {
        return $this->orderRepository->getById($orderId);
    }
}
