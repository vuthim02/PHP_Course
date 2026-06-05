<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Board;
use App\Models\BoardList;

class BoardController
{
    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }
    }

    public function index(): void
    {
        $this->requireAuth();
        $boards = Board::findByUser((int) Session::get('user_id'));
        View::render('boards/index', ['boards' => $boards]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();
        $board = Board::find($id);
        if (!$board) { View::render('errors/404'); return; }

        $lists = $board->lists();
        View::render('boards/show', ['board' => $board, 'lists' => $lists]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $board = new Board();
        $board->title = $_POST['title'] ?? '';
        $board->description = $_POST['description'] ?? '';
        $board->user_id = (int) Session::get('user_id');
        $board->save();

        // Create default lists
        foreach (['To Do', 'In Progress', 'Done'] as $i => $name) {
            $list = new BoardList();
            $list->title = $name;
            $list->board_id = $board->id;
            $list->position = $i;
            $list->save();
        }

        Session::flash('success', 'Board created.');
        View::redirect('/boards/' . $board->id);
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();
        $board = Board::find($id);
        if ($board && $board->user_id === (int) Session::get('user_id')) {
            $board->delete();
            Session::flash('success', 'Board deleted.');
        }
        View::redirect('/boards');
    }
}
