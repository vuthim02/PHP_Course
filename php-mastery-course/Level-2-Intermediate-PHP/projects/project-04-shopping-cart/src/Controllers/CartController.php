<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Services\CartService;

class CartController
{
    public function show(): void
    {
        $cartService = new CartService();
        $cart = $cartService->getCart();
        $items = $cart->getItems();
        $total = $cart->total();
        View::render('cart/show', ['items' => $items, 'total' => $total, 'cart' => $cart]);
    }

    public function add(): void
    {
        $productId = (int) Request::post('product_id');
        $quantity = max(1, (int) Request::post('quantity', 1));

        $cartService = new CartService();
        $cartService->addItem($productId, $quantity);

        Session::flash('success', 'Item added to cart.');
        View::redirect('/cart');
    }

    public function update(): void
    {
        $productId = (int) Request::post('product_id');
        $quantity = (int) Request::post('quantity', 0);

        $cartService = new CartService();
        $cartService->updateItem($productId, $quantity);

        Session::flash('success', 'Cart updated.');
        View::redirect('/cart');
    }

    public function remove(): void
    {
        $productId = (int) Request::post('product_id');

        $cartService = new CartService();
        $cartService->removeItem($productId);

        Session::flash('success', 'Item removed.');
        View::redirect('/cart');
    }
}
