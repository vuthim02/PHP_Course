<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;

class AdminController
{
    public function __construct()
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Admin access required.');
            View::redirect('/login');
        }
    }

    public function dashboard(): void
    {
        $totalPosts = Post::all();
        $totalUsers = User::all();
        $totalComments = Comment::all();
        $totalCategories = Category::all();

        View::render('admin/dashboard', [
            'postCount'     => count($totalPosts),
            'userCount'     => count($totalUsers),
            'commentCount'  => count($totalComments),
            'categoryCount' => count($totalCategories),
        ]);
    }

    public function posts(): void
    {
        $status = $_GET['status'] ?? null;
        $posts = Post::all($status);

        View::render('admin/posts', ['posts' => $posts]);
    }

    public function comments(): void
    {
        $status = $_GET['status'] ?? null;
        $comments = Comment::all($status);

        View::render('admin/comments', ['comments' => $comments]);
    }

    public function users(): void
    {
        $users = User::all();
        View::render('admin/users', ['users' => $users]);
    }

    public function categories(): void
    {
        $categories = Category::all();
        View::render('admin/categories', ['categories' => $categories]);
    }

    public function storeCategory(): void
    {
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            View::redirect('/admin/categories');
        }

        $category = new Category();
        $category->name = $name;
        $category->slug = Category::generateSlug($name);
        $category->description = $_POST['description'] ?? '';
        $category->save();

        Session::flash('success', 'Category created.');
        View::redirect('/admin/categories');
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
        }

        Session::flash('success', 'Category deleted.');
        View::redirect('/admin/categories');
    }

    public function deleteUser(int $id): void
    {
        $user = User::find($id);
        if ($user && $user->id !== (int) Session::get('user_id')) {
            // Delete user's posts and comments first, then user
            $db = \App\Core\Database::getInstance();
            $db->delete('comments', 'user_id = ?', [$id]);
            $db->delete('posts', 'user_id = ?', [$id]);
            $db->delete('users', 'id = ?', [$id]);
        }

        Session::flash('success', 'User removed.');
        View::redirect('/admin/users');
    }

    public function updateUserRole(int $id): void
    {
        $user = User::find($id);
        if ($user && $user->id !== (int) Session::get('user_id')) {
            $user->role = $_POST['role'] ?? 'user';
            $user->save();
        }

        Session::flash('success', 'User role updated.');
        View::redirect('/admin/users');
    }
}
