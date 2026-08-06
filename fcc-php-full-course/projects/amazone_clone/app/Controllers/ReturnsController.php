<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;

class ReturnsController
{
    public function landing(): void
    {
        $myReturns = current_user() !== null ? ReturnRequest::forUser(current_user()["id"]) : [];
        $pageTitle = "Returns & Replacements";

        require __DIR__ . "/../views/returns/landing.php";
    }

    public function start(): void
    {
        require_auth();

        $orders = Order::ordersForUser(current_user()["id"]);

        // Step 2 (when ?order=N): show that order's items for selection.
        $orderId  = (int) ($_GET["order"] ?? 0);
        $selected = null;
        $items    = [];
        if ($orderId > 0) {
            $result = Order::withItems($orderId);
            if ($result !== null && (int) $result["order"]["user_id"] === current_user()["id"]) {
                $selected = $result["order"];
                $items    = $result["items"];
            }
        }

        $pageTitle = "Start a return";
        require __DIR__ . "/../views/returns/start.php";
    }

    public function show(string $returnId): void
    {
        require_auth();

        $result = ReturnRequest::withItems((int) $returnId);
        if ($result === null || (int) $result["request"]["user_id"] !== current_user()["id"]) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $request   = $result["request"];
        $items     = $result["items"];
        $pageTitle = "Return #" . $returnId;

        require __DIR__ . "/../views/returns/show.php";
    }

    public function submit(): void
    {
        csrf_verify();
        require_auth();

        $orderId  = (int) ($_POST["order_id"] ?? 0);
        $itemIds  = array_map("intval", (array) ($_POST["items"] ?? []));
        $itemIds  = array_values(array_filter($itemIds, fn (int $id) => $id > 0));
        $reason   = trim((string) ($_POST["reason"] ?? ""));

        // Guard: the order must exist and belong to the current user.
        $result = Order::withItems($orderId);
        if ($result === null || (int) $result["order"]["user_id"] !== current_user()["id"]) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        if (empty($itemIds) || $reason === "") {
            $_SESSION["flash"] = "Pick at least one item and a return reason.";
            redirect("/returns/start?order=" . $orderId);
        }

        try {
            ReturnRequest::create(current_user()["id"], $orderId, $itemIds, $reason);
        } catch (\RuntimeException $e) {
            $_SESSION["flash"] = $e->getMessage();
            redirect("/returns/start?order=" . $orderId);
        }

        $_SESSION["flash"] = "Your return request has been submitted. You'll get an email with the next steps.";
        redirect("/returns");
    }
}
