<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Card
{
    public ?int $id = null;
    public string $title;
    public ?string $description = null;
    public int $board_list_id;
    public ?int $assigned_user_id = null;
    public ?string $due_date = null;
    public int $position = 0;
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $data = Database::getInstance()->fetch('SELECT * FROM cards WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByList(int $listId): array
    {
        $rows = Database::getInstance()->fetchAll(
            'SELECT c.*, u.username as assigned_username
             FROM cards c
             LEFT JOIN users u ON c.assigned_user_id = u.id
             WHERE c.board_list_id = ?
             ORDER BY c.position ASC', [$listId]
        );
        return array_map(fn($r) => self::hydrate($r), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $data = [
            'title'            => $this->title,
            'description'      => $this->description,
            'board_list_id'    => $this->board_list_id,
            'assigned_user_id' => $this->assigned_user_id,
            'due_date'         => $this->due_date,
            'position'         => $this->position,
        ];
        if ($this->id) {
            $db->update('cards', $data, 'id = ?', [$this->id]);
            return $this->id;
        }
        return $db->insert('cards', $data);
    }

    public function delete(): void
    {
        Database::getInstance()->delete('cards', 'id = ?', [$this->id]);
    }

    public function moveToList(int $newListId, int $newPosition): void
    {
        $this->board_list_id = $newListId;
        $this->position = $newPosition;
        $this->save();
    }

    public function assignedUser(): ?User
    {
        return $this->assigned_user_id ? User::find($this->assigned_user_id) : null;
    }

    public function list(): ?BoardList
    {
        return BoardList::find($this->board_list_id);
    }

    private static function hydrate(object $data): self
    {
        $c = new self();
        $c->id = (int) $data->id;
        $c->title = $data->title;
        $c->description = $data->description ?? null;
        $c->board_list_id = (int) $data->board_list_id;
        $c->assigned_user_id = isset($data->assigned_user_id) ? (int) $data->assigned_user_id : null;
        $c->due_date = $data->due_date ?? null;
        $c->position = (int) $data->position;
        $c->created_at = $data->created_at ?? null;
        return $c;
    }
}
