<?php

declare(strict_types=1);

namespace DDD\Tests\Application;

use PHPUnit\Framework\TestCase;
use DDD\Application\OrderService;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Shared\ValueObjects\Address;
use DDD\Infrastructure\Persistence\DoctrineOrderRepository;
use DDD\Infrastructure\Event\InMemoryEventBus;

final class OrderServiceTest extends TestCase
{
    private OrderService $orderService;
    private DoctrineOrderRepository $orderRepository;
    private InMemoryEventBus $eventBus;

    protected function setUp(): void
    {
        $this->orderRepository = new DoctrineOrderRepository();
        $this->eventBus = new InMemoryEventBus();
        $this->orderService = new OrderService($this->orderRepository, $this->eventBus);
    }

    public function testPlaceOrderPersistsOrder(): void
    {
        $shipping = new Address('123 Main', 'City', 'ST', '12345', 'US');
        $billing = new Address('456 Oak', 'City', 'ST', '12345', 'US');
        $items = [
            ['productId' => 'P1', 'productName' => 'Product 1', 'price' => 10.00, 'currency' => 'USD', 'quantity' => 1],
        ];

        $order = $this->orderService->placeOrder('CUST-001', $shipping, $billing, $items);

        $retrieved = $this->orderRepository->getById($order->id());
        $this->assertNotNull($retrieved);
        $this->assertTrue($retrieved->id()->equals($order->id()));
    }
}
