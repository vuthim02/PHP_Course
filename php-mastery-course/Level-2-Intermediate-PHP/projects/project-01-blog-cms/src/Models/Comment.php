<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Comment
{
    public ?int $id = null;
    public string $content;
    public int $post_id;
    public int $user_id;
    public string $status = 'approved';
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM comments WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByPost(int $postId, string $status = 'approved'): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            'SELECT c.*, u.username
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.post_id = ? AND c.status = ?
             ORDER BY c.created_at ASC',
            [$postId, $status]
        );
        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public static function all(string $status = null): array
    {
        $db = Database::getInstance();

        if ($status) {
            $rows = $db->fetchAll(
                'SELECT c.*, u.username, p.title as post_title
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 JOIN posts p ON c.post_id = p.id
                 WHERE c.status = ?
                 ORDER BY c.created_at DESC',
                [$status]
            );
        } else {
            $rows = $db->fetchAll(
                'SELECT c.*, u.username, p.title as post_title
                 FROM comments c
                 JOIN users u ON c.user_id = u.id
                 JOIN posts p ON c.post_id = p.id
                 ORDER BY c.created_at DESC'
            );
        }

        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();

        if ($this->id) {
            $db->update('comments', [
                'content' => $this->content,
                'status'  => $this->status,
            ], 'id = ?', [$this->id]);
            return $this->id;
        }

        return $db->insert('comments', [
            'content' => $this->content,
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'status'  => $this->status,
        ]);
    }

    public function delete(): void
    {
        $db = Database::getInstance();
        $db->delete('comments', 'id = ?', [$this->id]);
    }

    public function author(): ?User
    {
        return User::find($this->user_id);
    }

    public function post(): ?Post
    {
        return Post::find($this->post_id);
    }

    private static function hydrate(object $data): self
    {
        $comment = new self();
        $comment->id = (int) $data->id;
        $comment->content = $data->content;
        $comment->post_id = (int) $data->post_id;
        $comment->user_id = (int) $data->user_id;
        $comment->status = $data->status;
        $comment->created_at = $data->created_at ?? null;
        return $comment;
    }
}
