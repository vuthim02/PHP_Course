<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Post
{
    public ?int $id = null;
    public string $title;
    public string $slug;
    public string $content;
    public ?string $excerpt = null;
    public int $user_id;
    public ?int $category_id = null;
    public string $status = 'draft';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function find(int $id): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM posts WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM posts WHERE slug = ?', [$slug]);
        return $data ? self::hydrate($data) : null;
    }

    public static function all(string $status = null): array
    {
        $db = Database::getInstance();

        if ($status) {
            $rows = $db->fetchAll(
                'SELECT p.*, u.username, c.name as category_name
                 FROM posts p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.status = ?
                 ORDER BY p.created_at DESC',
                [$status]
            );
        } else {
            $rows = $db->fetchAll(
                'SELECT p.*, u.username, c.name as category_name
                 FROM posts p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN categories c ON p.category_id = c.id
                 ORDER BY p.created_at DESC'
            );
        }

        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public static function findByCategory(int $categoryId, string $status = 'published'): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            'SELECT p.*, u.username, c.name as category_name
             FROM posts p
             JOIN users u ON p.user_id = u.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.status = ?
             ORDER BY p.created_at DESC',
            [$categoryId, $status]
        );
        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();

        $data = [
            'title'       => $this->title,
            'slug'        => $this->slug,
            'content'     => $this->content,
            'excerpt'     => $this->excerpt,
            'user_id'     => $this->user_id,
            'category_id' => $this->category_id,
            'status'      => $this->status,
        ];

        if ($this->id) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $db->update('posts', $data, 'id = ?', [$this->id]);
            return $this->id;
        }

        return $db->insert('posts', $data);
    }

    public function delete(): void
    {
        $db = Database::getInstance();
        $db->delete('posts', 'id = ?', [$this->id]);
    }

    public function author(): ?User
    {
        return User::find($this->user_id);
    }

    public function category(): ?Category
    {
        return $this->category_id ? Category::find($this->category_id) : null;
    }

    public function comments(): array
    {
        return Comment::findByPost($this->id);
    }

    public static function paginate(int $page = 1, int $perPage = 10, string $status = 'published'): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;

        $total = $db->fetch('SELECT COUNT(*) as count FROM posts WHERE status = ?', [$status]);
        $totalPages = (int) ceil((int) $total->count / $perPage);

        $rows = $db->fetchAll(
            'SELECT p.*, u.username, c.name as category_name
             FROM posts p
             JOIN users u ON p.user_id = u.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = ?
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?',
            [$status, $perPage, $offset]
        );

        return [
            'items'       => array_map(fn($row) => self::hydrate($row), $rows),
            'currentPage' => $page,
            'perPage'     => $perPage,
            'total'       => (int) $total->count,
            'totalPages'  => $totalPages,
        ];
    }

    public static function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $db = Database::getInstance();
        $count = $db->fetch('SELECT COUNT(*) as count FROM posts WHERE slug LIKE ?', ["{$slug}%"]);
        return $count->count > 0 ? "{$slug}-{$count->count}" : $slug;
    }

    private static function hydrate(object $data): self
    {
        $post = new self();
        $post->id = (int) $data->id;
        $post->title = $data->title;
        $post->slug = $data->slug;
        $post->content = $data->content;
        $post->excerpt = $data->excerpt ?? null;
        $post->user_id = (int) $data->user_id;
        $post->category_id = isset($data->category_id) ? (int) $data->category_id : null;
        $post->status = $data->status;
        $post->created_at = $data->created_at ?? null;
        $post->updated_at = $data->updated_at ?? null;
        return $post;
    }
}
