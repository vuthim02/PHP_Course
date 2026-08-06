<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Review;

class ProductController
{
    public function index(): void
    {
        $categoryId = (int) ($_GET["category"] ?? 0);
        $products   = Product::all($categoryId);
        $categories = Product::categories();

        require __DIR__ . "/../views/products/list.php";
    }

    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            require __DIR__ . "/../views/404.php";
            return;
        }

        $reviews = Review::forProduct((int) $product["id"]);
        $myReview = null;
        if (current_user() !== null) {
            $myReview = Review::userReview((int) $product["id"], current_user()["id"]);
        }

        require __DIR__ . "/../views/products/show.php";
    }

    public function search(): void
    {
        $q = trim((string) ($_GET["q"] ?? ""));

        $products   = $q === "" ? [] : Product::search($q);
        $categories = Product::categories();
        $categoryId = 0;
        $pageTitle  = $q === "" ? "Search" : 'Results for "' . $q . '"';

        require __DIR__ . "/../views/products/list.php";
    }
}
