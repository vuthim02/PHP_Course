<?php

declare(strict_types=1);

namespace CQRSES\Domain\ValueObject;

/**
 * AccountId Value Object
 *
 * Strongly-typed identifier for the Account aggregate.
 */
final class AccountId
{
    public function __construct(private string $id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Account ID cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self('ACC-' . strtoupper(bin2hex(random_bytes(8))));
    }

    public function id(): string { return $this->id; }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
