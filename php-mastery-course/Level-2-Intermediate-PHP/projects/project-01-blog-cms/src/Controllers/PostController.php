<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;

class PostController
{
    public function index(): void
    {
        $page = (int) (Request::get('page', 1));
        $category = Request::get('category');

        if ($category) {
            $cat = Category::findBySlug($category);
            if ($cat) {
                $pagination = Post::paginate($page, 10, 'published');
                $posts = $pagination['items'];
                View::render('posts/index', [
                    'posts'      => $posts,
                    'pagination' => $pagination,
                    'category'   => $cat,
                ]);
                return;
            }
        }

        $pagination = Post::paginate($page, 10, 'published');
        $posts = $pagination['items'];

        View::render('posts/index', [
            'posts'      => $posts,
            'pagination' => $pagination,
        ]);
    }

    public function show(string $slug): void
    {
        $post = Post::findBySlug($slug);

        if (!$post || $post->status !== 'published') {
            View::render('errors/404');
            return;
        }

        $comments = $post->comments();
        View::render('posts/show', [
            'post'     => $post,
            'comments' => $comments,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $categories = Category::all();
        View::render('posts/form', ['categories' => $categories, 'post' => null]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $title = Request::post('title');
        $content = Request::post('content');
        $categoryId = Request::post('category_id');
        $status = Request::post('status', 'draft');

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Title and content are required.');
            View::redirect('/posts/create');
        }

        $post = new Post();
        $post->title = $title;
        $post->slug = Post::generateSlug($title);
        $post->content = $content;
        $post->excerpt = Request::post('excerpt');
        $post->user_id = (int) Session::get('user_id');
        $post->category_id = $categoryId ? (int) $categoryId : null;
        $post->status = $status;

        $post->save();

        Session::flash('success', 'Post created successfully.');
        View::redirect('/posts/' . $post->slug);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $post = Post::find($id);

        if (!$post) {
            View::render('errors/404');
            return;
        }

        $this->authorizePost($post);

        $categories = Category::all();
        View::render('posts/form', ['post' => $post, 'categories' => $categories]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $post = Post::find($id);

        if (!$post) {
            View::render('errors/404');
            return;
        }

        $this->authorizePost($post);

        $post->title = Request::post('title');
        $post->content = Request::post('content');
        $post->excerpt = Request::post('excerpt');
        $post->category_id = (int) (Request::post('category_id')) ?: null;
        $post->status = Request::post('status', 'draft');

        $post->save();

        Session::flash('success', 'Post updated successfully.');
        View::redirect('/posts/' . $post->slug);
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();
        $post = Post::find($id);

        if (!$post) {
            View::render('errors/404');
            return;
        }

        $this->authorizePost($post);
        $post->delete();

        Session::flash('success', 'Post deleted successfully.');
        View::redirect('/');
    }

    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login first.');
            View::redirect('/login');
        }
    }

    private function authorizePost(Post $post): void
    {
        if ($post->user_id !== (int) Session::get('user_id') && Session::get('user_role') !== 'admin') {
            Session::flash('error', 'You are not authorized.');
            View::redirect('/');
        }
    }
}
