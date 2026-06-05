<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function processPayment(Order $order, string $method): Payment
    {
        // Stripe mock implementation
        $stripePaymentId = 'pi_' . Str::random(24);
        $status = 'completed';

        // Simulate payment processing
        if ($method === 'stripe') {
            $status = $this->mockStripeCharge($order, $stripePaymentId);
        } elseif ($method === 'paypal') {
            $status = $this->mockPaypalCharge($order, $stripePaymentId);
        } elseif ($method === 'cod') {
            $status = 'pending';
        }

        return Payment::create([
            'order_id' => $order->id,
            'stripe_payment_id' => $stripePaymentId,
            'amount' => $order->total,
            'currency' => 'usd',
            'status' => $status,
            'method' => $method,
        ]);
    }

    private function mockStripeCharge(Order $order, string $paymentId): string
    {
        // In production, this would call Stripe\Charge::create()
        return 'completed';
    }

    private function mockPaypalCharge(Order $order, string $paymentId): string
    {
        // In production, this would call PayPal REST API
        return 'completed';
    }
}
