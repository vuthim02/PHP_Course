<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::active()
            ->with(['category', 'variants'])
            ->withCount('variants');

        if ($request->filled('category')) {
            $query->byCategory((int) $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        $sortField = in_array($request->sort, ['price', 'name', 'created_at']) ? $request->sort : 'created_at';
        $sortDir = $request->direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDir);

        $perPage = min((int) $request->per_page, 50) ?: 15;
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'variants']);

        return new ProductResource($product);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'unique:products,sku'],
            'is_active' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created.',
            'product' => new ProductResource($product->load('category')),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'unique:products,sku,' . $product->id],
            'is_active' => ['boolean'],
            'category_id' => ['sometimes', 'exists:categories,id'],
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated.',
            'product' => new ProductResource($product->fresh()->load('category')),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->variants()->delete();
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }
}
