<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\Comment;

class CommentController
{
    public function store(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login to comment.');
            View::redirect('/login');
        }

        $postId = (int) Request::post('post_id');
        $content = Request::post('content');

        if (empty($content)) {
            Session::flash('error', 'Comment cannot be empty.');
            View::redirect('/posts/' . Request::post('post_slug'));
        }

        $comment = new Comment();
        $comment->content = $content;
        $comment->post_id = $postId;
        $comment->user_id = (int) Session::get('user_id');

        $comment->save();

        Session::flash('success', 'Comment added.');
        View::redirect('/posts/' . Request::post('post_slug'));
    }

    public function destroy(int $id): void
    {
        if (!Session::has('user_id')) {
            View::redirect('/login');
        }

        $comment = Comment::find($id);
        if (!$comment) {
            View::render('errors/404');
            return;
        }

        if ($comment->user_id !== (int) Session::get('user_id') && Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Not authorized.');
            View::redirect('/');
        }

        $comment->delete();
        Session::flash('success', 'Comment deleted.');
        View::redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    public function approve(int $id): void
    {
        if (Session::get('user_role') !== 'admin') {
            View::redirect('/login');
        }

        $comment = Comment::find($id);
        if ($comment) {
            $comment->status = 'approved';
            $comment->save();
        }

        View::redirect('/admin/comments');
    }
}
