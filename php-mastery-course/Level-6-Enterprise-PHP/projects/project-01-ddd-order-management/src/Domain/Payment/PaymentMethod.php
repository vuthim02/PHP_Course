<?php

declare(strict_types=1);

namespace DDD\Domain\Payment;

/**
 * PaymentMethod Value Object
 *
 * Represents a payment method within the Payment bounded context.
 */
final class PaymentMethod
{
    public const CREDIT_CARD = 'credit_card';
    public const DEBIT_CARD = 'debit_card';
    public const PAYPAL = 'paypal';
    public const BANK_TRANSFER = 'bank_transfer';

    private const VALID_METHODS = [
        self::CREDIT_CARD,
        self::DEBIT_CARD,
        self::PAYPAL,
        self::BANK_TRANSFER,
    ];

    public function __construct(private string $type)
    {
        if (!in_array($type, self::VALID_METHODS, true)) {
            throw new \InvalidArgumentException("Invalid payment method: {$type}");
        }
    }

    public function type(): string { return $this->type; }

    public function isOnline(): bool
    {
        return in_array($this->type, [self::CREDIT_CARD, self::DEBIT_CARD, self::PAYPAL], true);
    }

    public function equals(self $other): bool
    {
        return $this->type === $other->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
