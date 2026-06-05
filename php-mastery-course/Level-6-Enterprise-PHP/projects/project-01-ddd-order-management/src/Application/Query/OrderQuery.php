<?php

declare(strict_types=1);

namespace DDD\Application\Query;

use DDD\Domain\Order\Order;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Order\OrderRepositoryInterface;

/**
 * OrderQuery
 *
 * Read-side query for orders.
 * In CQRS, this would use a separate read model/database.
 * For now it reads from the same repository, but the interface is separated
 * to allow future read-model optimization (denormalized views, caching, etc.).
 */
final class OrderQuery
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function getOrder(OrderId $orderId): ?Order
    {
        return $this->orderRepository->getById($orderId);
    }

    public function getOrdersByCustomer(string $customerId): array
    {
        return $this->orderRepository->findByCustomerId($customerId);
    }

    public function getOrderSummary(OrderId $orderId): ?array
    {
        $order = $this->orderRepository->getById($orderId);
        if ($order === null) {
            return null;
        }

        return [
            'id' => (string) $order->id(),
            'customerId' => $order->customerId(),
            'status' => (string) $order->status(),
            'total' => $order->total()->toFloat(),
            'currency' => $order->total()->currency(),
            'itemCount' => count($order->items()),
            'shippingAddress' => (string) $order->shippingAddress(),
        ];
    }
}
