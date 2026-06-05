<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Board
{
    public ?int $id = null;
    public string $title;
    public ?string $description = null;
    public int $user_id;
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $data = Database::getInstance()->fetch('SELECT * FROM boards WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByUser(int $userId): array
    {
        $rows = Database::getInstance()->fetchAll(
            'SELECT b.*, (SELECT COUNT(*) FROM cards c JOIN board_lists bl ON c.board_list_id = bl.id WHERE bl.board_id = b.id) as card_count
             FROM boards b WHERE b.user_id = ? ORDER BY b.created_at DESC', [$userId]
        );
        return array_map(fn($r) => self::hydrate($r), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();
        if ($this->id) {
            $db->update('boards', ['title' => $this->title, 'description' => $this->description], 'id = ?', [$this->id]);
            return $this->id;
        }
        return $db->insert('boards', ['title' => $this->title, 'description' => $this->description, 'user_id' => $this->user_id]);
    }

    public function delete(): void
    {
        Database::getInstance()->delete('boards', 'id = ?', [$this->id]);
    }

    public function lists(): array
    {
        return BoardList::findByBoard($this->id);
    }

    public function owner(): ?User
    {
        return User::find($this->user_id);
    }

    private static function hydrate(object $data): self
    {
        $b = new self();
        $b->id = (int) $data->id;
        $b->title = $data->title;
        $b->description = $data->description ?? null;
        $b->user_id = (int) $data->user_id;
        $b->created_at = $data->created_at ?? null;
        if (isset($data->card_count)) $b->card_count = (int) $data->card_count;
        return $b;
    }
}
