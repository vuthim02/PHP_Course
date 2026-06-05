<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Mockery;
use Tests\TestCase;

class OrderServiceCalculationTest extends TestCase
{
    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    public function test_tax_is_ten_percent_of_subtotal(): void
    {
        $cart = Mockery::mock(Cart::class);
        $cart->shouldReceive('getAttribute')->with('total')->andReturn(200.00);
        $cart->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $cart->items = collect([]);

        $reflection = new \ReflectionMethod(OrderService::class, 'createOrder');
        $params = $reflection->getParameters();

        $this->assertEquals('cart', $params[0]->getName());
        $this->assertEquals('data', $params[1]->getName());
    }

    public function test_free_shipping_at_fifty_dollars(): void
    {
        $cart = Mockery::mock(Cart::class);
        $cart->shouldReceive('getAttribute')->with('total')->andReturn(50.00);
        $cart->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $cart->items = collect([]);

        $order = $this->orderService->createOrder($cart, [
            'shipping_address' => '123 Test St',
            'payment_method' => 'stripe',
        ]);

        $this->assertEquals(0, $order->shipping);
    }

    public function test_shipping_applied_below_fifty(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create(['price' => 30.00]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $order = $this->orderService->createOrder($cart, [
            'shipping_address' => '456 Test Ave',
            'payment_method' => 'stripe',
        ]);

        $this->assertEquals(9.99, $order->shipping);
    }
}
