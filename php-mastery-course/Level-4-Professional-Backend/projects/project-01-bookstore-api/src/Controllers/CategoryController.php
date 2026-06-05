<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Category;

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(Request $request): Response
    {
        $includeBookCount = (bool) $request->getQueryParam('with_books', false);
        $categories = $this->categoryModel->findAll($includeBookCount);
        return Response::success($categories);
    }

    public function show(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $category = $this->categoryModel->findById($id);

        if (!$category) {
            return Response::error('Category not found', 404);
        }

        return Response::success($category);
    }

    public function store(Request $request): Response
    {
        $data = $request->getBody();

        if (empty($data['name'])) {
            return Response::error('Category name is required', 422);
        }

        $id = $this->categoryModel->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
        ]);

        return Response::created($this->categoryModel->findById($id));
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->categoryModel->findById($id);

        if (!$existing) {
            return Response::error('Category not found', 404);
        }

        $data = $request->getBody();
        $updateData = [];

        foreach (['name', 'description'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $this->categoryModel->update($id, $updateData);
        }

        return Response::success($this->categoryModel->findById($id));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->categoryModel->findById($id);

        if (!$existing) {
            return Response::error('Category not found', 404);
        }

        $this->categoryModel->delete($id);
        return Response::noContent();
    }
}
