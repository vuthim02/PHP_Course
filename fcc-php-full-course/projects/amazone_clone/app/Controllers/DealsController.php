<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;

class DealsController
{
    public function index(): void
    {
        $categoryId = (int) ($_GET["category"] ?? 0);
        $deals      = Product::deals($categoryId);
        $dealOfDay  = Product::dealOfTheDay();
        $categories = Product::categories();

        $categoryName = "";
        if ($categoryId > 0) {
            foreach ($categories as $cat) {
                if ((int) $cat["id"] === $categoryId) {
                    $categoryName = $cat["name"];
                    break;
                }
            }
        }

        $pageTitle = "Today's Deals";
        require __DIR__ . "/../views/deals.php";
    }
}
