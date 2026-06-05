<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Notifications\CommentNotification;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $comment = $post->comments()->create([
            'body' => $request->validated()['body'],
            'user_id' => $request->user()->id,
            'is_approved' => false,
        ]);

        $post->author->notify(new CommentNotification($comment));

        return back()->with('success', 'Your comment has been submitted and is pending approval.');
    }
}
