<?php

declare(strict_types=1);

namespace DDD\Domain\Shipping;

use DDD\Domain\Shared\ValueObjects\Address;

/**
 * ShippingAddress Value Object
 *
 * Extends the shared Address with shipping-specific metadata.
 * Part of the Shipping bounded context.
 */
final class ShippingAddress
{
    public function __construct(
        private Address $address,
        private ?string $deliveryInstructions = null,
        private ?string $phoneNumber = null
    ) {
    }

    public function address(): Address { return $this->address; }
    public function deliveryInstructions(): ?string { return $this->deliveryInstructions; }
    public function phoneNumber(): ?string { return $this->phoneNumber; }

    public function equals(self $other): bool
    {
        return $this->address->equals($other->address)
            && $this->deliveryInstructions === $other->deliveryInstructions
            && $this->phoneNumber === $other->phoneNumber;
    }
}
