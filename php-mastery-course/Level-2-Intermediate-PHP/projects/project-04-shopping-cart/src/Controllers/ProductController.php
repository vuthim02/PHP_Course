<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Models\Product;

class ProductController
{
    public function index(): void
    {
        $page = (int) (Request::get('page', 1));
        $pagination = Product::paginate($page);
        View::render('products/index', ['pagination' => $pagination]);
    }

    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) { View::render('errors/404'); return; }
        View::render('products/show', ['product' => $product]);
    }
}
