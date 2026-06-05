<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Author;

class AuthorController
{
    private Author $authorModel;

    public function __construct()
    {
        $this->authorModel = new Author();
    }

    public function index(Request $request): Response
    {
        $page = (int) ($request->getQueryParam('page', 1));
        $perPage = min((int) ($request->getQueryParam('per_page', 20)), 100);

        $result = $this->authorModel->findAll($page, $perPage);

        return Response::paginated($result['items'], $result['total'], $page, $perPage, '/v1/authors');
    }

    public function show(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $author = $this->authorModel->findById($id);

        if (!$author) {
            return Response::error('Author not found', 404);
        }

        return Response::success($author);
    }

    public function store(Request $request): Response
    {
        $data = $request->getBody();

        if (empty($data['name'])) {
            return Response::error('Author name is required', 422);
        }

        if (strlen($data['name']) > 255) {
            return Response::error('Author name must not exceed 255 characters', 422);
        }

        $id = $this->authorModel->create([
            'name' => $data['name'],
            'bio' => $data['bio'] ?? '',
            'birth_date' => $data['birth_date'] ?? null,
        ]);

        $author = $this->authorModel->findById($id);
        return Response::created($author);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->authorModel->findById($id);

        if (!$existing) {
            return Response::error('Author not found', 404);
        }

        $data = $request->getBody();
        $updateData = [];

        foreach (['name', 'bio', 'birth_date'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $this->authorModel->update($id, $updateData);
        }

        return Response::success($this->authorModel->findById($id));
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->getRouteParam();
        $existing = $this->authorModel->findById($id);

        if (!$existing) {
            return Response::error('Author not found', 404);
        }

        $this->authorModel->delete($id);
        return Response::noContent();
    }
}
