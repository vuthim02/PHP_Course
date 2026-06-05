<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Card;
use App\Models\BoardList;

class CardController
{
    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }
    }

    public function store(): void
    {
        $this->requireAuth();
        $listId = (int) ($_POST['board_list_id'] ?? 0);
        $list = BoardList::find($listId);
        if (!$list) { View::redirect('/boards'); return; }

        $card = new Card();
        $card->title = $_POST['title'] ?? '';
        $card->description = $_POST['description'] ?? '';
        $card->board_list_id = $listId;
        $card->due_date = $_POST['due_date'] ?: null;
        $card->assigned_user_id = $_POST['assigned_user_id'] ? (int) $_POST['assigned_user_id'] : null;

        $cards = Card::findByList($listId);
        $card->position = count($cards);
        $card->save();

        Session::flash('success', 'Card added.');
        View::redirect('/boards/' . $list->board_id);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $card = Card::find($id);
        if (!$card) { View::redirect('/boards'); return; }

        $card->title = $_POST['title'] ?? $card->title;
        $card->description = $_POST['description'] ?? $card->description;
        $card->due_date = $_POST['due_date'] ?: null;
        $card->assigned_user_id = isset($_POST['assigned_user_id']) ? (int) $_POST['assigned_user_id'] : $card->assigned_user_id;
        $card->save();

        Session::flash('success', 'Card updated.');
        View::redirect('/boards/' . $card->list()->board_id);
    }

    public function move(int $id): void
    {
        $this->requireAuth();
        $card = Card::find($id);
        if (!$card) { View::redirect('/boards'); return; }

        $newListId = (int) ($_POST['board_list_id'] ?? 0);
        $newPosition = (int) ($_POST['position'] ?? 0);

        $card->moveToList($newListId, $newPosition);
        Session::flash('success', 'Card moved.');
        View::redirect('/boards/' . $card->list()->board_id);
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();
        $card = Card::find($id);
        if ($card) {
            $boardId = $card->list()->board_id;
            $card->delete();
            Session::flash('success', 'Card deleted.');
            View::redirect('/boards/' . $boardId);
        }
        View::redirect('/boards');
    }
}
