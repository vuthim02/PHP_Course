<?php

declare(strict_types=1);

namespace DDD\Tests\Domain\Order;

use PHPUnit\Framework\TestCase;
use DDD\Domain\Order\OrderStatus;

final class OrderStatusTest extends TestCase
{
    public function testPendingToConfirmedIsValid(): void
    {
        $status = OrderStatus::pending();
        $newStatus = $status->transitionTo(OrderStatus::confirmed());
        $this->assertTrue($newStatus->equals(OrderStatus::confirmed()));
    }

    public function testPendingToCancelledIsValid(): void
    {
        $status = OrderStatus::pending();
        $newStatus = $status->transitionTo(OrderStatus::cancelled());
        $this->assertTrue($newStatus->equals(OrderStatus::cancelled()));
    }

    public function testDeliveredToCancelledIsInvalid(): void
    {
        $this->expectException(\DomainException::class);
        $status = new OrderStatus(OrderStatus::DELIVERED);
        $status->transitionTo(OrderStatus::cancelled());
    }

    public function testInvalidStatusThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OrderStatus('invalid_status');
    }
}
