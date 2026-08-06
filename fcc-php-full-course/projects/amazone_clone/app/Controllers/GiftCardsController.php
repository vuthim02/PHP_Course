<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;

class GiftCardsController
{
    public function index(): void
    {
        $giftCards = Product::byCategorySlug("gift-cards");
        $pageTitle = "amazone Gift Cards";

        require __DIR__ . "/../views/gift-cards.php";
    }
}
