<?php

declare(strict_types=1);

namespace DDD\Infrastructure\Persistence;

use DDD\Domain\Payment\Payment;
use DDD\Domain\Payment\PaymentRepositoryInterface;
use DDD\Domain\Order\OrderId;

/**
 * InMemoryPaymentRepository
 *
 * In-memory implementation of PaymentRepositoryInterface.
 */
final class InMemoryPaymentRepository implements PaymentRepositoryInterface
{
    /** @var array<string, Payment> */
    private array $payments = [];

    public function save(Payment $payment): void
    {
        $this->payments[$payment->id()] = $payment;
    }

    public function getById(string $id): ?Payment
    {
        return $this->payments[$id] ?? null;
    }

    public function findByOrderId(OrderId $orderId): ?Payment
    {
        foreach ($this->payments as $payment) {
            if ($payment->orderId()->equals($orderId)) {
                return $payment;
            }
        }
        return null;
    }
}
