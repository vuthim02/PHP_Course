<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Post
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT p.*, u.name as author_name
             FROM posts p JOIN users u ON p.author_id = u.id
             WHERE p.id = ?",
            [$id]
        );
    }

    public function findAll(int $page = 1, int $perPage = 20, ?string $search = null): array
    {
        $where = '';
        $params = [];

        if ($search) {
            $where = 'WHERE p.title LIKE ? OR p.content LIKE ?';
            $term = '%' . $search . '%';
            $params = [$term, $term];
        }

        $offset = ($page - 1) * $perPage;

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as count FROM posts p {$where}",
            $params
        );

        $items = $this->db->fetchAll(
            "SELECT p.*, u.name as author_name
             FROM posts p JOIN users u ON p.author_id = u.id
             {$where}
             ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items' => $items,
            'total' => (int) ($countResult['count'] ?? 0),
        ];
    }

    public function create(array $data): int
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->db->insert('posts', $data);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = time();
        return $this->db->update('posts', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        $this->db->delete('post_tags', 'post_id = ?', [$id]);
        $this->db->delete('comments', 'post_id = ?', [$id]);
        return $this->db->delete('posts', 'id = ?', [$id]);
    }

    public function getComments(int $postId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT c.*, u.name as user_name
             FROM comments c JOIN users u ON c.user_id = u.id
             WHERE c.post_id = ? ORDER BY c.created_at ASC LIMIT ? OFFSET ?",
            [$postId, $perPage, $offset]
        );
    }

    public function getCommentCount(int $postId): int
    {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM comments WHERE post_id = ?", [$postId]);
        return (int) ($result['count'] ?? 0);
    }

    public function getTags(int $postId): array
    {
        return $this->db->fetchAll(
            "SELECT t.* FROM tags t
             JOIN post_tags pt ON t.id = pt.tag_id
             WHERE pt.post_id = ?",
            [$postId]
        );
    }

    public function attachTags(int $postId, array $tagIds): void
    {
        foreach ($tagIds as $tagId) {
            $this->db->insert('post_tags', ['post_id' => $postId, 'tag_id' => $tagId]);
        }
    }
}
