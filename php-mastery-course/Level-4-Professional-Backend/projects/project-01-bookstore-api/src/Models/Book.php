<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Book
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT b.*, a.name as author_name, c.name as category_name
             FROM books b
             JOIN authors a ON b.author_id = a.id
             JOIN categories c ON b.category_id = c.id
             WHERE b.id = ?",
            [$id]
        );
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(b.title LIKE ? OR a.name LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['author_id'])) {
            $where[] = 'b.author_id = ?';
            $params[] = (int) $filters['author_id'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'b.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'b.price >= ?';
            $params[] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'b.price <= ?';
            $params[] = (float) $filters['max_price'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $allowedSorts = ['title', 'price', 'created_at', 'published_year'];
        $sort = $filters['sort'] ?? 'created_at';
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';

        $order = strtoupper($filters['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as count FROM books b
             LEFT JOIN authors a ON b.author_id = a.id
             {$whereClause}",
            $params
        );

        $books = $this->db->fetchAll(
            "SELECT b.*, a.name as author_name, c.name as category_name
             FROM books b
             JOIN authors a ON b.author_id = a.id
             JOIN categories c ON b.category_id = c.id
             {$whereClause}
             ORDER BY b.{$sort} {$order}
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items' => $books,
            'total' => (int) ($countResult['count'] ?? 0),
        ];
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->db->insert('books', $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = time();
        return $this->db->update('books', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('books', 'id = ?', [$id]);
    }

    public function count(): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM books");
        return (int) ($result['count'] ?? 0);
    }
}
