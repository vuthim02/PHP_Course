<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Order;
use App\Services\CartService;

class OrderController
{
    public function index(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }

        $orders = Order::findByUser((int) Session::get('user_id'));
        View::render('orders/index', ['orders' => $orders]);
    }

    public function show(int $id): void
    {
        if (!Session::has('user_id')) {
            View::redirect('/login');
        }

        $order = Order::find($id);
        if (!$order || $order->user_id !== (int) Session::get('user_id')) {
            Session::flash('error', 'Order not found.');
            View::redirect('/orders');
        }

        $items = $order->items();
        View::render('orders/show', ['order' => $order, 'items' => $items]);
    }

    public function checkout(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login to checkout.');
            View::redirect('/login');
        }

        $cartService = new CartService();
        $cart = $cartService->getCart();

        if ($cart->isEmpty()) {
            Session::flash('error', 'Your cart is empty.');
            View::redirect('/cart');
        }

        try {
            $order = $cartService->checkout((int) Session::get('user_id'));
            View::redirect('/orders/' . $order->id);
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            View::redirect('/cart');
        }
    }
}
