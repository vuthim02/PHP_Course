<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function show(Request $request): CartResource
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $cart->load(['items.product', 'items.variant']);

        return new CartResource($cart);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $cart = $this->cartService->getOrCreateCart($request->user());
        $this->cartService->addItem($cart, $validated);

        $cart->load(['items.product', 'items.variant']);

        return response()->json([
            'message' => 'Item added to cart.',
            'cart' => new CartResource($cart),
        ]);
    }

    public function updateItem(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
            abort(403, 'This item does not belong to your cart.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if ($validated['quantity'] === 0) {
            $cartItem->delete();
            $message = 'Item removed from cart.';
        } else {
            $cartItem->update(['quantity' => $validated['quantity']]);
            $message = 'Cart item updated.';
        }

        $cart = $cartItem->cart->fresh(['items.product', 'items.variant']);

        return response()->json([
            'message' => $message,
            'cart' => new CartResource($cart),
        ]);
    }

    public function removeItem(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        $cartItem->delete();

        $cart = $cartItem->cart->fresh(['items.product', 'items.variant']);

        return response()->json([
            'message' => 'Item removed from cart.',
            'cart' => new CartResource($cart),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $cart->items()->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }
}
