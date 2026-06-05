<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Book;
use App\Validators\BookValidator;
use OpenApi\Attributes as OA;

class BookController
{
    private Book $bookModel;

    public function __construct()
    {
        $this->bookModel = new Book();
    }

    public function index(Request $request): Response
    {
        $page = (int) ($request->getQueryParam('page', 1));
        $perPage = min((int) ($request->getQueryParam('per_page', 20)), 100);
        $filters = [
            'search' => $request->getQueryParam('search'),
            'author_id' => $request->getQueryParam('author_id'),
            'category_id' => $request->getQueryParam('category_id'),
            'min_price' => $request->getQueryParam('min_price'),
            'max_price' => $request->getQueryParam('max_price'),
            'sort' => $request->getQueryParam('sort'),
            'order' => $request->getQueryParam('order'),
        ];

        $filters = array_filter($filters, fn($v) => $v !== null);

        $result = $this->bookModel->findAll($filters, $page, $perPage);

        return Response::paginated(
            $result['items'],
            $result['total'],
            $page,
            $perPage,
            '/v1/books'
        );
    }

    public function show(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $book = $this->bookModel->findById($id);

        if (!$book) {
            return Response::error('Book not found', 404);
        }

        return Response::success($book);
    }

    public function store(Request $request): Response
    {
        $data = $request->getBody();

        $errors = BookValidator::validateCreate($data);
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $bookId = $this->bookModel->create([
            'title' => $data['title'],
            'author_id' => (int) $data['author_id'],
            'category_id' => (int) $data['category_id'],
            'isbn' => $data['isbn'],
            'price' => (float) $data['price'],
            'stock' => (int) ($data['stock'] ?? 0),
            'description' => $data['description'] ?? '',
            'published_year' => (int) ($data['published_year'] ?? date('Y')),
            'cover_image' => $data['cover_image'] ?? '',
        ]);

        $book = $this->bookModel->findById($bookId);
        return Response::created($book);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->bookModel->findById($id);

        if (!$existing) {
            return Response::error('Book not found', 404);
        }

        $data = $request->getBody();

        $errors = BookValidator::validateUpdate($data);
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $updateData = [];
        $allowedFields = ['title', 'author_id', 'category_id', 'isbn', 'price', 'stock', 'description', 'published_year', 'cover_image'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = match ($field) {
                    'author_id', 'category_id', 'published_year', 'stock' => (int) $data[$field],
                    'price' => (float) $data[$field],
                    default => $data[$field],
                };
            }
        }

        if (!empty($updateData)) {
            $this->bookModel->update($id, $updateData);
        }

        $book = $this->bookModel->findById($id);
        return Response::success($book);
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->bookModel->findById($id);

        if (!$existing) {
            return Response::error('Book not found', 404);
        }

        $this->bookModel->delete($id);
        return Response::noContent();
    }
}
