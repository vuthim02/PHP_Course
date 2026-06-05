<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/search', SearchController::class)->name('search');
Route::get('/feed', RssFeedController::class)->name('feed');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('posts', PostController::class, ['as' => 'admin']);
        Route::resource('categories', CategoryController::class, ['as' => 'admin']);
        Route::resource('tags', TagController::class, ['as' => 'admin']);
        Route::resource('users', UserController::class, ['as' => 'admin']);
        Route::patch('/comments/{comment}/approve', [AdminController::class, 'approveComment'])->name('comments.approve');
        Route::delete('/comments/{comment}', [AdminController::class, 'destroyComment'])->name('comments.destroy');
    });
});
