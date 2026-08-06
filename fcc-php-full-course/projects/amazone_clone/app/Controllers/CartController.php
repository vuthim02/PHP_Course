<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;

class CartController
{
    public function add(): void
    {
        csrf_verify();

        $productId = (int) ($_POST["product_id"] ?? 0);
        $qty       = max(1, (int) ($_POST["quantity"] ?? 1));

        if ($productId > 0) {
            Cart::add($productId, $qty);
        }

        header("Content-Type: application/json");
        echo json_encode(["count" => Cart::count()]);
    }

    public function index(): void
    {
        $items   = Cart::items();
        $summary = Cart::summary();
        $pageTitle = "Shopping Cart";

        require __DIR__ . "/../views/cart.php";
    }

    public function update(): void
    {
        csrf_verify();
        Cart::update((int) ($_POST["id"] ?? 0), (int) ($_POST["quantity"] ?? 0));
        redirect("/cart");
    }

    public function remove(): void
    {
        csrf_verify();
        Cart::remove((int) ($_POST["id"] ?? 0));
        redirect("/cart");
    }
}
