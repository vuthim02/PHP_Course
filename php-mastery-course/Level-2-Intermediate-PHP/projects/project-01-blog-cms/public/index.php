<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\Request;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\AdminController;
use App\Controllers\CommentController;

Session::start();

$router = new Router();

// Public routes
$router->get('/', [PostController::class, 'index']);

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Post routes
$router->get('/posts/create', [PostController::class, 'create']);
$router->post('/posts', [PostController::class, 'store']);
$router->get('/posts/{slug}', [PostController::class, 'show']);
$router->get('/posts/{id}/edit', [PostController::class, 'edit']);
$router->post('/posts/{id}/update', [PostController::class, 'update']);
$router->post('/posts/{id}/delete', [PostController::class, 'destroy']);

// Comment routes
$router->post('/comments', [CommentController::class, 'store']);
$router->post('/comments/{id}/delete', [CommentController::class, 'destroy']);

// Admin routes
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/posts', [AdminController::class, 'posts']);
$router->get('/admin/comments', [AdminController::class, 'comments']);
$router->get('/admin/comments/{id}/approve', [CommentController::class, 'approve']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser']);
$router->post('/admin/users/{id}/role', [AdminController::class, 'updateUserRole']);
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->post('/admin/categories', [AdminController::class, 'storeCategory']);
$router->post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory']);

$router->dispatch(Request::method(), Request::uri());
