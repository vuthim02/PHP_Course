<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Review;
use App\Models\Book;

class ReviewController
{
    private Review $reviewModel;
    private Book $bookModel;

    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->bookModel = new Book();
    }

    public function index(Request $request): Response
    {
        $bookId = (int) $request->getRouteParam();
        $book = $this->bookModel->findById($bookId);

        if (!$book) {
            return Response::error('Book not found', 404);
        }

        $page = (int) ($request->getQueryParam('page', 1));
        $perPage = min((int) ($request->getQueryParam('per_page', 20)), 100);

        $result = $this->reviewModel->findByBook($bookId, $page, $perPage);

        return Response::paginated(
            $result['items'],
            $result['total'],
            $page,
            $perPage,
            "/v1/books/{$bookId}/reviews"
        );
    }

    public function store(Request $request): Response
    {
        $bookId = (int) $request->getRouteParam();
        $book = $this->bookModel->findById($bookId);

        if (!$book) {
            return Response::error('Book not found', 404);
        }

        $data = $request->getBody();
        $errors = [];

        if (empty($data['rating'])) {
            $errors['rating'] = 'Rating is required';
        } elseif (!is_numeric($data['rating']) || (int) $data['rating'] < 1 || (int) $data['rating'] > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5';
        }

        if (empty($data['comment'])) {
            $errors['comment'] = 'Comment is required';
        } elseif (strlen($data['comment']) > 2000) {
            $errors['comment'] = 'Comment must not exceed 2000 characters';
        }

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $reviewId = $this->reviewModel->create([
            'book_id' => $bookId,
            'user_id' => (int) $request->getRouteParam(),
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'],
        ]);

        $review = $this->reviewModel->findById($reviewId);
        return Response::created($review);
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $review = $this->reviewModel->findById($id);

        if (!$review) {
            return Response::error('Review not found', 404);
        }

        $this->reviewModel->delete($id);
        return Response::noContent();
    }
}
