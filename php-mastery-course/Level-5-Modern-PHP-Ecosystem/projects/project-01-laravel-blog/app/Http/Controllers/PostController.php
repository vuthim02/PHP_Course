<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function index(Request $request): View
    {
        $query = Post::with(['author', 'category', 'comments']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $posts = $query->latest()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->postService->createPost($request->validated());

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->updatePost($post, $request->validated());

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->deletePost($post);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
