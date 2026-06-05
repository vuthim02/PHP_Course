<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class BoardList
{
    public ?int $id = null;
    public string $title;
    public int $board_id;
    public int $position = 0;

    public static function find(int $id): ?self
    {
        $data = Database::getInstance()->fetch('SELECT * FROM board_lists WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByBoard(int $boardId): array
    {
        $rows = Database::getInstance()->fetchAll(
            'SELECT * FROM board_lists WHERE board_id = ? ORDER BY position ASC', [$boardId]
        );
        return array_map(fn($r) => self::hydrate($r), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();
        if ($this->id) {
            $db->update('board_lists', ['title' => $this->title, 'position' => $this->position], 'id = ?', [$this->id]);
            return $this->id;
        }
        return $db->insert('board_lists', ['title' => $this->title, 'board_id' => $this->board_id, 'position' => $this->position]);
    }

    public function delete(): void
    {
        Database::getInstance()->delete('board_lists', 'id = ?', [$this->id]);
    }

    public function cards(): array
    {
        return Card::findByList($this->id);
    }

    public function board(): ?Board
    {
        return Board::find($this->board_id);
    }

    private static function hydrate(object $data): self
    {
        $l = new self();
        $l->id = (int) $data->id;
        $l->title = $data->title;
        $l->board_id = (int) $data->board_id;
        $l->position = (int) $data->position;
        return $l;
    }
}
