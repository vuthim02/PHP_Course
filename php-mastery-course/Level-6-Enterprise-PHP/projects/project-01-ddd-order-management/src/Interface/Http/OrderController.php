<?php

declare(strict_types=1);

namespace DDD\Interface\Http;

use DDD\Application\OrderService;
use DDD\Application\Query\OrderQuery;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Shared\ValueObjects\Address;

/**
 * OrderController
 *
 * HTTP entry point for order operations.
 * In hexagonal architecture, the HTTP controller is an inbound adapter
 * that translates HTTP requests into application service calls.
 * No business logic here — just request parsing and response formatting.
 */
final class OrderController
{
    public function __construct(
        private OrderService $orderService,
        private OrderQuery $orderQuery
    ) {
    }

    /**
     * POST /orders
     */
    public function placeOrder(array $requestData): string
    {
        $shipping = new Address(
            $requestData['shipping']['street'],
            $requestData['shipping']['city'],
            $requestData['shipping']['state'],
            $requestData['shipping']['zip'],
            $requestData['shipping']['country']
        );

        $billing = new Address(
            $requestData['billing']['street'],
            $requestData['billing']['city'],
            $requestData['billing']['state'],
            $requestData['billing']['zip'],
            $requestData['billing']['country']
        );

        $order = $this->orderService->placeOrder(
            $requestData['customerId'],
            $shipping,
            $billing,
            $requestData['items']
        );

        return json_encode([
            'orderId' => (string) $order->id(),
            'status' => (string) $order->status(),
            'total' => $order->total()->toFloat(),
        ]);
    }

    /**
     * DELETE /orders/{id}
     */
    public function cancelOrder(string $orderId, string $reason): string
    {
        $this->orderService->cancelOrder(new OrderId($orderId), $reason);

        return json_encode(['status' => 'cancelled', 'orderId' => $orderId]);
    }

    /**
     * GET /orders/{id}
     */
    public function getOrder(string $orderId): string
    {
        $summary = $this->orderQuery->getOrderSummary(new OrderId($orderId));

        if ($summary === null) {
            http_response_code(404);
            return json_encode(['error' => 'Order not found']);
        }

        return json_encode($summary);
    }
}
