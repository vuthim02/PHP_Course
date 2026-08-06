<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Stripe\Client as Stripe;

class CheckoutController
{
    public function index(): void
    {
        $items = Cart::items();
        if (empty($items)) {
            redirect("/cart");
        }

        $summary = Cart::summary();

        // Prefill shipping fields from the signed-in user's saved address.
        $profile = null;
        if (current_user() !== null) {
            $profile = User::find(current_user()["id"]);
        }

        $pageTitle = "Checkout";

        require __DIR__ . "/../views/checkout.php";
    }

    public function create(): void
    {
        csrf_verify();

        $address = [
            "shipping_address" => trim((string) ($_POST["shipping_address"] ?? "")),
            "city"             => trim((string) ($_POST["city"] ?? "")),
            "zip"              => trim((string) ($_POST["zip"] ?? "")),
        ];

        if ($address["shipping_address"] === "" || $address["city"] === "" || $address["zip"] === "") {
            $_SESSION["flash"] = "Please fill in all shipping fields.";
            redirect("/checkout");
        }

        // Remember the address on the user's profile for next time.
        if (current_user() !== null) {
            User::updateShippingAddress(current_user()["id"], $address);
        }

        $summary = Cart::summary();

        // Create the order + consume stock BEFORE charging.
        $orderId = Order::create($address, $summary["total"]);

        try {
            $intent = Stripe::createPaymentIntent(
                (int) round($summary["total"] * 100),
                $orderId
            );
        } catch (\Throwable $e) {
            // Roll the pending order back so nothing is left half-created.
            db()->prepare("DELETE FROM order_items WHERE order_id = :id")->execute([":id" => $orderId]);
            db()->prepare("DELETE FROM orders WHERE id = :id")->execute([":id" => $orderId]);

            $_SESSION["flash"] = "Payment setup failed: " . $e->getMessage();
            redirect("/checkout");
        }

        $clientSecret = $intent["client_secret"];
        $pubKey       = config("stripe.publishable_key", "");
        $pageTitle    = "Complete your purchase";

        require __DIR__ . "/../views/checkout_pay.php";
    }

    public function complete(): void
    {
        csrf_verify();

        $intentId = trim((string) ($_POST["payment_intent"] ?? ""));

        try {
            $intent = Stripe::getPaymentIntent($intentId);
        } catch (\Throwable) {
            redirect("/cart");
        }

        if (($intent["status"] ?? "") === "succeeded") {
            $orderId = (int) ($intent["metadata"]["order_id"] ?? 0);
            if ($orderId > 0) {
                Order::markPaid($orderId);
                redirect("/order/" . $orderId);
            }
        }

        $_SESSION["flash"] = "Payment was not completed. No charge was made.";
        redirect("/checkout");
    }

    public function order(string $orderId): void
    {
        $result = Order::withItems((int) $orderId);

        if ($result === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $pageTitle = "Order #" . $orderId;
        require __DIR__ . "/../views/order.php";
    }
}
