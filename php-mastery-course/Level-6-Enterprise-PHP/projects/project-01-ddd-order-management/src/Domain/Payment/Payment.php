<?php

declare(strict_types=1);

namespace DDD\Domain\Payment;

use DDD\Domain\Shared\ValueObjects\Money;
use DDD\Domain\Order\OrderId;

/**
 * Payment Entity
 *
 * Represents a payment transaction in the Payment bounded context.
 * Not an aggregate root — created and managed by payment application service.
 */
final class Payment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public function __construct(
        private string $id,
        private OrderId $orderId,
        private Money $amount,
        private PaymentMethod $method,
        private string $status = self::STATUS_PENDING
    ) {
    }

    public function complete(string $transactionId): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \DomainException('Only pending payments can be completed');
        }
        $this->status = self::STATUS_COMPLETED;
        $this->transactionId = $transactionId;
    }

    public function fail(string $reason): void
    {
        $this->status = self::STATUS_FAILED;
        $this->failureReason = $reason;
    }

    public function refund(): void
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new \DomainException('Only completed payments can be refunded');
        }
        $this->status = self::STATUS_REFUNDED;
    }

    private ?string $transactionId = null;
    private ?string $failureReason = null;

    public function id(): string { return $this->id; }
    public function orderId(): OrderId { return $this->orderId; }
    public function amount(): Money { return $this->amount; }
    public function method(): PaymentMethod { return $this->method; }
    public function status(): string { return $this->status; }
    public function transactionId(): ?string { return $this->transactionId; }
    public function failureReason(): ?string { return $this->failureReason; }
}
