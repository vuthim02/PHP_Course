<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Review
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT r.*, u.name as user_name FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.id = ?",
            [$id]
        );
    }

    public function findByBook(int $bookId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT r.*, u.name as user_name FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.book_id = ? ORDER BY r.created_at DESC LIMIT ? OFFSET ?",
            [$bookId, $perPage, $offset]
        );

        $total = $this->db->fetch("SELECT COUNT(*) as count FROM reviews WHERE book_id = ?", [$bookId]);

        return [
            'items' => $items,
            'total' => (int) ($total['count'] ?? 0),
        ];
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->db->insert('reviews', $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = time();
        return $this->db->update('reviews', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('reviews', 'id = ?', [$id]);
    }

    public function getAverageRating(int $bookId): float
    {
        $result = $this->db->fetch(
            "SELECT AVG(rating) as avg_rating FROM reviews WHERE book_id = ?",
            [$bookId]
        );
        return round((float) ($result['avg_rating'] ?? 0), 1);
    }
}
