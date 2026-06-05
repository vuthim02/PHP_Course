<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService,
    ) {}

    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => ['required', 'array'],
            'shipping_address.line1' => ['required', 'string'],
            'shipping_address.city' => ['required', 'string'],
            'shipping_address.postal_code' => ['required', 'string'],
            'shipping_address.country' => ['required', 'string', 'size:2'],
            'billing_address' => ['sometimes', 'array'],
            'payment_method' => ['required', 'string', 'in:stripe,paypal,cod'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = $this->cartService->getOrCreateCart($request->user());

        if ($cart->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $order = $this->orderService->createOrder($cart, $validated);

        try {
            $payment = $this->paymentService->processPayment($order, $validated['payment_method']);
            $order->update(['payment_status' => $payment->status]);

            $cart->items()->delete();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => new OrderResource($order->load(['items', 'payment'])),
            ], 201);
        } catch (\Exception $e) {
            $order->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment failed: ' . $e->getMessage()], 422);
        }
    }
}
