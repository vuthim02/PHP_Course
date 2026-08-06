<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;

class HomeController
{
    public function index(): void
    {
        $products   = Product::featured(8);
        $categories = Product::categories();
        $dealOfDay  = Product::dealOfTheDay();

        require __DIR__ . "/../views/home.php";
    }
}
