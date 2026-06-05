<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;

class OrderService
{
    public function createOrder(Cart $cart, array $data): Order
    {
        $subtotal = $cart->total;
        $tax = $subtotal * 0.1;
        $shipping = $subtotal >= 50 ? 0 : 9.99;
        $total = $subtotal + $tax + $shipping;

        $order = Order::create([
            'user_id' => $cart->user_id,
            'status' => Order::STATUS_PENDING,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product->name,
                'sku' => $item->product->sku,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => $item->unit_price * $item->quantity,
            ]);

            $item->product->decrement('stock_quantity', $item->quantity);
        }

        return $order;
    }
}
