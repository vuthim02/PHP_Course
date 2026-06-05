<?php

declare(strict_types=1);

namespace CQRSES\Domain\ValueObject;

/**
 * Amount Value Object
 *
 * Immutable monetary amount stored in cents.
 */
final class Amount
{
    public function __construct(private int $cents)
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public static function fromFloat(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public function cents(): int { return $this->cents; }

    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(self $other): self
    {
        if ($other->cents > $this->cents) {
            throw new \DomainException('Insufficient funds');
        }
        return new self($this->cents - $other->cents);
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function __toString(): string
    {
        return sprintf('%01.2f', $this->toFloat());
    }
}
