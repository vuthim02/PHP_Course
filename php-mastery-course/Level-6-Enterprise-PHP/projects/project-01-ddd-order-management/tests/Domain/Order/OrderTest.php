<?php

declare(strict_types=1);

namespace DDD\Tests\Domain\Order;

use PHPUnit\Framework\TestCase;
use DDD\Domain\Order\Order;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Order\OrderItem;
use DDD\Domain\Order\OrderStatus;
use DDD\Domain\Shared\ValueObjects\Address;
use DDD\Domain\Shared\ValueObjects\Money;

/**
 * Tests for the Order aggregate root.
 * Domain testing: business rules and invariants should be validated
 * at the domain layer without any infrastructure dependencies.
 */
final class OrderTest extends TestCase
{
    private function createSampleOrder(): Order
    {
        $id = OrderId::generate();
        $shipping = new Address('123 Main', 'City', 'ST', '12345', 'US');
        $billing = new Address('456 Oak', 'City', 'ST', '12345', 'US');
        $items = [
            new OrderItem('P1', 'Product 1', Money::fromFloat(10.00), 2),
            new OrderItem('P2', 'Product 2', Money::fromFloat(25.50), 1),
        ];

        return Order::place($id, 'CUST-001', $shipping, $billing, $items);
    }

    public function testPlaceOrderCreatesWithPendingStatus(): void
    {
        $order = $this->createSampleOrder();
        $this->assertTrue($order->status()->equals(OrderStatus::pending()));
    }

    public function testPlaceOrderWithNoItemsThrowsException(): void
    {
        $this->expectException(\DomainException::class);
        $id = OrderId::generate();
        $addr = new Address('123 Main', 'City', 'ST', '12345', 'US');
        Order::place($id, 'CUST-001', $addr, $addr, []);
    }

    public function testPlaceOrderRecordsDomainEvent(): void
    {
        $order = $this->createSampleOrder();
        $events = $order->recordedEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(\DDD\Domain\Order\Event\OrderPlacedEvent::class, $events[0]);
    }

    public function testOrderTotalCalculatesCorrectly(): void
    {
        $order = $this->createSampleOrder();
        // 10.00 * 2 + 25.50 * 1 = 45.50
        $this->assertEquals(4550, $order->total()->amount());
        $this->assertEquals(45.50, $order->total()->toFloat());
    }

    public function testCancelOrderChangesStatus(): void
    {
        $order = $this->createSampleOrder();
        $order->cancel('Customer changed mind');
        $this->assertTrue($order->status()->equals(OrderStatus::cancelled()));
    }

    public function testCancelOrderRecordsEvent(): void
    {
        $order = $this->createSampleOrder();
        $order->clearEvents();
        $order->cancel('Test reason');
        $events = $order->recordedEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(\DDD\Domain\Order\Event\OrderCancelledEvent::class, $events[0]);
    }

    public function testConfirmOrderTransitionsCorrectly(): void
    {
        $order = $this->createSampleOrder();
        $order->confirm();
        $this->assertTrue($order->status()->equals(OrderStatus::confirmed()));
    }

    public function testInvalidStatusTransitionThrowsException(): void
    {
        $this->expectException(\DomainException::class);
        $order = $this->createSampleOrder();
        $order->markDelivered(); // Cannot go from pending to delivered directly
    }
}
