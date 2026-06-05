<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function addItem(Cart $cart, array $data): void
    {
        $product = Product::findOrFail($data['product_id']);

        $unitPrice = $product->price;
        if (isset($data['product_variant_id'])) {
            $variant = ProductVariant::findOrFail($data['product_variant_id']);
            $unitPrice = $variant->price ?? $product->price;
        }

        $existingItem = $cart->items()
            ->where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $data['quantity']);
        } else {
            $cart->items()->create([
                'product_id' => $data['product_id'],
                'product_variant_id' => $data['product_variant_id'] ?? null,
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
            ]);
        }
    }
}
