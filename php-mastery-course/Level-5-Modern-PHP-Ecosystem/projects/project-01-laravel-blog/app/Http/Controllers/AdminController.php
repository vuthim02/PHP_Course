<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public static function postsNamespace(): string
    {
        return 'App\Http\Controllers\PostController';
    }

    public static function categoriesNamespace(): string
    {
        return 'App\Http\Controllers\CategoryController';
    }

    public static function tagsNamespace(): string
    {
        return 'App\Http\Controllers\TagController';
    }

    public static function usersNamespace(): string
    {
        return 'App\Http\Controllers\UserController';
    }

    public function dashboard(): View
    {
        $stats = [
            'posts_count' => Post::count(),
            'published_posts' => Post::published()->count(),
            'pending_comments' => Comment::pending()->count(),
            'users_count' => User::count(),
            'categories_count' => Category::count(),
            'tags_count' => Tag::count(),
            'recent_posts' => Post::with('author')->latest()->take(5)->get(),
            'recent_comments' => Comment::with(['user', 'post'])->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function approveComment(Comment $comment): RedirectResponse
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved successfully.');
    }

    public function destroyComment(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }
}
