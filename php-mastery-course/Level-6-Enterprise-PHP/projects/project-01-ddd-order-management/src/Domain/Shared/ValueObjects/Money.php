<?php

declare(strict_types=1);

namespace DDD\Domain\Shared\ValueObjects;

/**
 * Money Value Object
 *
 * Immutable representation of monetary value with currency.
 * Part of the Shared Kernel — used across multiple bounded contexts.
 * Value objects have no identity, are compared by their properties.
 */
final class Money
{
    private int $amount;  // Stored in cents to avoid floating-point issues
    private string $currency;

    public function __construct(int $amount, string $currency = 'USD')
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
        if (empty($currency) || strlen($currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be a 3-letter ISO code');
        }
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public static function fromFloat(float $amount, string $currency = 'USD'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Cannot add Money with different currencies');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Cannot subtract Money with different currencies');
        }
        if ($other->amount > $this->amount) {
            throw new \DomainException('Insufficient funds');
        }
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return sprintf('%s %01.2f', $this->currency, $this->toFloat());
    }
}
