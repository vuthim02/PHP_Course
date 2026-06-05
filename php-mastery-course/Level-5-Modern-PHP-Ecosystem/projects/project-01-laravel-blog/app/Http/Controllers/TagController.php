<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(9);

        return view('tags.show', compact('tag', 'posts'));
    }
}
