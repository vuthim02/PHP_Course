<?php

declare(strict_types=1);

namespace DDD\Infrastructure\Services;

use DDD\Domain\Payment\Payment;
use DDD\Domain\Shared\ValueObjects\Money;

/**
 * MockPaymentGateway
 *
 * Infrastructure adapter simulating a payment gateway (Stripe, PayPal, etc.).
 * Part of the hexagonal architecture's driven adapters.
 */
final class MockPaymentGateway
{
    /**
     * Process a payment through the external gateway.
     */
    public function charge(Money $amount, string $paymentMethodNonce): array
    {
        // Simulate gateway processing
        $success = random_int(0, 10) > 1; // 90% success rate

        if (!$success) {
            return [
                'success' => false,
                'error' => 'Card declined: insufficient funds',
                'transactionId' => null,
            ];
        }

        return [
            'success' => true,
            'error' => null,
            'transactionId' => 'TXN-' . strtoupper(bin2hex(random_bytes(12))),
        ];
    }

    /**
     * Issue a refund through the gateway.
     */
    public function refund(string $transactionId): array
    {
        return [
            'success' => true,
            'refundId' => 'RFND-' . strtoupper(bin2hex(random_bytes(12))),
        ];
    }
}
