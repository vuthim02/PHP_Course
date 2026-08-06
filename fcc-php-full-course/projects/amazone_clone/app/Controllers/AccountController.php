<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Models\User;

class AccountController
{
    public function profile(): void
    {
        $user = current_user();
        if ($user === null) {
            redirect("/login");
        }

        $profile   = User::find($user["id"]);
        $pageTitle = "Your Account";

        require __DIR__ . "/../views/account/profile.php";
    }

    public function orders(): void
    {
        $user = current_user();
        if ($user === null) {
            redirect("/login");
        }

        $orders    = Order::ordersForUser($user["id"]);
        $pageTitle = "Your Orders";

        require __DIR__ . "/../views/account/orders.php";
    }

    public function orderDetail(string $orderId): void
    {
        $user = current_user();
        if ($user === null) {
            redirect("/login");
        }

        $result = Order::withItems((int) $orderId);
        if ($result === null || (int) $result["order"]["user_id"] !== $user["id"]) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $order     = $result["order"];
        $items     = $result["items"];
        $pageTitle = "Order #" . $orderId;

        require __DIR__ . "/../views/account/order-detail.php";
    }
}
