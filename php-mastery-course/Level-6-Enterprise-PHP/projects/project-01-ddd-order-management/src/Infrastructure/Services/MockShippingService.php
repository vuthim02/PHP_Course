<?php

declare(strict_types=1);

namespace DDD\Infrastructure\Services;

use DDD\Domain\Shipping\ShippingServiceInterface;
use DDD\Domain\Shipping\ShippingAddress;
use DDD\Domain\Order\OrderId;

/**
 * MockShippingService
 *
 * Infrastructure-level implementation of the shipping domain service.
 * In production, this would call a real carrier API (FedEx, UPS, etc.).
 * The hexagon: infrastructure adapters implement domain ports (interfaces).
 */
final class MockShippingService implements ShippingServiceInterface
{
    /** @var array<string, array> */
    private array $shipments = [];

    public function createShipment(OrderId $orderId, ShippingAddress $address): string
    {
        $trackingNumber = 'SHIP-' . strtoupper(bin2hex(random_bytes(8)));

        $this->shipments[$trackingNumber] = [
            'orderId' => (string) $orderId,
            'address' => $address,
            'status' => 'created',
            'createdAt' => date('c'),
        ];

        echo "[Shipping] Shipment {$trackingNumber} created for order {$orderId}\n";

        return $trackingNumber;
    }

    public function getTrackingStatus(string $trackingNumber): string
    {
        return $this->shipments[$trackingNumber]['status'] ?? 'unknown';
    }

    public function cancelShipment(string $trackingNumber): void
    {
        if (isset($this->shipments[$trackingNumber])) {
            $this->shipments[$trackingNumber]['status'] = 'cancelled';
            echo "[Shipping] Shipment {$trackingNumber} cancelled\n";
        }
    }
}
