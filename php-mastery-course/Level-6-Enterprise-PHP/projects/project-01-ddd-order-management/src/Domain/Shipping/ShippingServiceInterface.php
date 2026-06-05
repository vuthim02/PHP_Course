<?php

declare(strict_types=1);

namespace DDD\Domain\Shipping;

use DDD\Domain\Order\OrderId;

/**
 * ShippingServiceInterface
 *
 * Domain service interface for shipping operations.
 * In DDD, a domain service encapsulates business logic that doesn't
 * naturally fit within an entity or value object.
 * The implementation is provided by the infrastructure layer.
 */
interface ShippingServiceInterface
{
    public function createShipment(OrderId $orderId, ShippingAddress $address): string;
    public function getTrackingStatus(string $trackingNumber): string;
    public function cancelShipment(string $trackingNumber): void;
}
