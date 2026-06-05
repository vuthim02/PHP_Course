<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        $posts = $category->publishedPosts()
            ->with(['author', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('categories.show', compact('category', 'posts'));
    }
}
