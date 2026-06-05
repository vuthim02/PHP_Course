<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    public function test_create_order_from_cart(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create([
            'price' => 29.99,
            'stock_quantity' => 100,
        ]);

        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price,
        ]);

        $order = $this->orderService->createOrder($cart, [
            'shipping_address' => '123 Main St',
            'payment_method' => 'stripe',
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertNotNull($order->total);
        $this->assertCount(1, $order->items);
    }

    public function test_order_calculates_tax_correctly(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create(['price' => 100.00]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $order = $this->orderService->createOrder($cart, [
            'shipping_address' => '456 Oak Ave',
            'payment_method' => 'stripe',
        ]);

        $this->assertEquals(100.00, $order->subtotal);
        $this->assertEquals(10.00, $order->tax); // 10% tax
    }

    public function test_free_shipping_above_threshold(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create(['price' => 60.00]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $order = $this->orderService->createOrder($cart, [
            'shipping_address' => '789 Pine Rd',
            'payment_method' => 'stripe',
        ]);

        $this->assertEquals(0, $order->shipping);
    }

    public function test_order_decrements_stock(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create([
            'price' => 15.00,
            'stock_quantity' => 10,
        ]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => $product->price,
        ]);

        $this->orderService->createOrder($cart, [
            'shipping_address' => '321 Elm St',
            'payment_method' => 'stripe',
        ]);

        $this->assertEquals(7, $product->fresh()->stock_quantity);
    }
}
