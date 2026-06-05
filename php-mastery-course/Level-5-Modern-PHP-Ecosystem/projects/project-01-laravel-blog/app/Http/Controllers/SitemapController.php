<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $xml .= '<url><loc>' . url('/') . '</loc><priority>1.0</priority></url>';

        foreach (Post::published()->latest('published_at')->get() as $post) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('posts.show', $post) . '</loc>';
            $xml .= '<lastmod>' . $post->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        foreach (Category::all() as $category) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('categories.show', $category) . '</loc>';
            $xml .= '<priority>0.5</priority>';
            $xml .= '</url>';
        }

        foreach (Tag::all() as $tag) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('tags.show', $tag) . '</loc>';
            $xml .= '<priority>0.3</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
