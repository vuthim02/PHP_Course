<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->take(50)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>' . config('app.name') . '</title>';
        $xml .= '<link>' . url('/') . '</link>';
        $xml .= '<description>Latest blog posts</description>';
        $xml .= '<language>en</language>';
        $xml .= '<atom:link href="' . route('feed') . '" rel="self" type="application/rss+xml"/>';

        foreach ($posts as $post) {
            $xml .= '<item>';
            $xml .= '<title>' . e($post->title) . '</title>';
            $xml .= '<link>' . route('posts.show', $post) . '</link>';
            $xml .= '<guid isPermaLink="true">' . route('posts.show', $post) . '</guid>';
            $xml .= '<description>' . e($post->excerpt) . '</description>';
            $xml .= '<pubDate>' . $post->published_at->toRssString() . '</pubDate>';
            $xml .= '<author>' . e($post->author->email) . ' (' . e($post->author->name) . ')</author>';
            if ($post->category) {
                $xml .= '<category>' . e($post->category->name) . '</category>';
            }
            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200, ['Content-Type' => 'application/rss+xml']);
    }
}
