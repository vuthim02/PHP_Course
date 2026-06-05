<?php

declare(strict_types=1);

namespace DDD\Domain\Shared\ValueObjects;

/**
 * Address Value Object
 *
 * Immutable address used across billing and shipping contexts.
 */
final class Address
{
    public function __construct(
        private string $street,
        private string $city,
        private string $state,
        private string $zipCode,
        private string $country
    ) {
    }

    public function street(): string { return $this->street; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
    public function zipCode(): string { return $this->zipCode; }
    public function country(): string { return $this->country; }

    public function equals(self $other): bool
    {
        return $this->street === $other->street
            && $this->city === $other->city
            && $this->state === $other->state
            && $this->zipCode === $other->zipCode
            && $this->country === $other->country;
    }

    public function __toString(): string
    {
        return "{$this->street}, {$this->city}, {$this->state} {$this->zipCode}, {$this->country}";
    }
}
