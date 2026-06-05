<?php

declare(strict_types=1);

namespace DDD\Application;

use DDD\Domain\Payment\Payment;
use DDD\Domain\Payment\PaymentMethod;
use DDD\Domain\Payment\PaymentRepositoryInterface;
use DDD\Domain\Shared\ValueObjects\Money;
use DDD\Domain\Order\OrderId;
use DDD\Infrastructure\Services\MockPaymentGateway;

/**
 * PaymentService
 *
 * Application service in the Payment bounded context.
 * Orchestrates the payment flow: validates, processes through gateway, persists.
 * Subscribes to OrderPlacedEvent to initiate payment.
 */
final class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private MockPaymentGateway $paymentGateway
    ) {
    }

    public function processPayment(OrderId $orderId, Money $amount, string $paymentMethodType): Payment
    {
        $method = new PaymentMethod($paymentMethodType);
        $payment = new Payment(
            uniqid('pay_', true),
            $orderId,
            $amount,
            $method
        );

        $result = $this->paymentGateway->charge($amount, $method->type());

        if ($result['success']) {
            $payment->complete($result['transactionId']);
            echo "[Payment] Payment {$payment->id()} completed for order {$orderId}\n";
        } else {
            $payment->fail($result['error']);
            echo "[Payment] Payment failed for order {$orderId}: {$result['error']}\n";
        }

        $this->paymentRepository->save($payment);

        return $payment;
    }

    public function refundPayment(string $paymentId): void
    {
        $payment = $this->paymentRepository->getById($paymentId);
        if ($payment === null) {
            throw new \RuntimeException("Payment {$paymentId} not found");
        }

        $this->paymentGateway->refund($payment->transactionId() ?? '');
        $payment->refund();
        $this->paymentRepository->save($payment);

        echo "[Payment] Payment {$paymentId} refunded\n";
    }
}
