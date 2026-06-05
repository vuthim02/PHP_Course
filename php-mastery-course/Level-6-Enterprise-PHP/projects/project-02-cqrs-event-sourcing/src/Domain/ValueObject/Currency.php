<?php

declare(strict_types=1);

namespace CQRSES\Domain\ValueObject;

/**
 * Currency Value Object
 *
 * ISO 4217 currency code.
 */
final class Currency
{
    private const VALID_CURRENCIES = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF'];

    public function __construct(private string $code)
    {
        $code = strtoupper($code);
        if (!in_array($code, self::VALID_CURRENCIES, true)) {
            throw new \InvalidArgumentException(
                "Invalid currency '{$code}'. Valid: " . implode(', ', self::VALID_CURRENCIES)
            );
        }
        $this->code = $code;
    }

    public function code(): string { return $this->code; }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
