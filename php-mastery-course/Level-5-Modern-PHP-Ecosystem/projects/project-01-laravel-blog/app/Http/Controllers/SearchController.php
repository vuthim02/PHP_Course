<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = $request->validate(['q' => 'required|string|max:200'])['q'];

        $posts = Post::published()
            ->search($query)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'searchQuery' => $query,
            'categories' => \App\Models\Category::has('publishedPosts')->get(),
            'tags' => \App\Models\Tag::has('posts')->get(),
        ]);
    }
}
