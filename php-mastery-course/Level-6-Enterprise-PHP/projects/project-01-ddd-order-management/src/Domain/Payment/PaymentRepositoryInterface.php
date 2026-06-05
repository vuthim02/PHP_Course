<?php

declare(strict_types=1);

namespace DDD\Domain\Payment;

use DDD\Domain\Order\OrderId;

/**
 * PaymentRepositoryInterface
 *
 * Repository for Payment entities in the Payment bounded context.
 */
interface PaymentRepositoryInterface
{
    public function save(Payment $payment): void;
    public function getById(string $id): ?Payment;
    public function findByOrderId(OrderId $orderId): ?Payment;
}
