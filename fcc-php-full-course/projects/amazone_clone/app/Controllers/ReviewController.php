<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Review;

class ReviewController
{
    public function store(): void
    {
        csrf_verify();
        require_auth();

        $productId = (int) ($_POST["product_id"] ?? 0);
        $rating    = (int) ($_POST["rating"] ?? 0);
        $comment   = trim((string) ($_POST["comment"] ?? ""));

        $product = $productId > 0 ? Product::findById($productId) : null;
        if ($product === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $slug = $product["slug"];

        if ($rating < 1 || $rating > 5) {
            $_SESSION["flash"] = "Please pick a star rating between 1 and 5.";
            redirect("/product/" . $slug);
        }

        if (Review::userReview($productId, current_user()["id"]) !== null) {
            $_SESSION["flash"] = "You've already reviewed this product.";
            redirect("/product/" . $slug);
        }

        Review::create($productId, current_user()["id"], $rating, $comment);
        $_SESSION["flash"] = "Thanks for your review!";
        redirect("/product/" . $slug);
    }

    public function destroy(): void
    {
        csrf_verify();
        require_auth();

        $reviewId  = (int) ($_POST["review_id"] ?? 0);
        $productId = (int) ($_POST["product_id"] ?? 0);

        $product = $productId > 0 ? Product::findById($productId) : null;
        if ($product === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $slug   = $product["slug"];
        $review = Review::userReview($productId, current_user()["id"]);

        if ($review !== null && (int) $review["id"] === $reviewId) {
            Review::delete($reviewId, $productId);
            $_SESSION["flash"] = "Your review was deleted.";
        }

        redirect("/product/" . $slug);
    }
}
