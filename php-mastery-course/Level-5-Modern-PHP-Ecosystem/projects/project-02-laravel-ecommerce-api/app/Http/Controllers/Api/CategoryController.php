<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount('products'));
    }

    public function products(Request $request, Category $category): AnonymousResourceCollection
    {
        $products = $category->activeProducts()
            ->with('variants')
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created.',
            'category' => new CategoryResource($category),
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated.',
            'category' => new CategoryResource($category),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json(['message' => 'Cannot delete category with existing products.'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }
}
