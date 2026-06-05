<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

Session::start();

// Remember me cookie check
if (!Session::has('user_id') && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $users = \App\Models\User::all();
    foreach ($users as $user) {
        if (password_verify($token, $user->remember_token ?? '')) {
            Session::set('user_id', $user->id);
            Session::set('user_role', $user->role);
            break;
        }
    }
}

$router = new Router();

// Home
$router->get('/', function() {
    if (\App\Core\Session::has('user_id')) {
        View::redirect('/profile');
    }
    View::redirect('/login');
});

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

// Profile routes (require auth)
$router->get('/profile', [ProfileController::class, 'show'], [AuthMiddleware::class]);
$router->post('/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class]);
$router->post('/profile/password', [ProfileController::class, 'changePassword'], [AuthMiddleware::class]);

// Admin routes (require auth; AdminController handles role check)
$router->get('/admin', [AdminController::class, 'dashboard'], [AuthMiddleware::class]);
$router->get('/admin/users', [AdminController::class, 'users'], [AuthMiddleware::class]);
$router->post('/admin/users/{id}/role', [AdminController::class, 'updateUserRole'], [AuthMiddleware::class]);
$router->get('/admin/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'], [AuthMiddleware::class]);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'], [AuthMiddleware::class]);
$router->get('/admin/roles', [AdminController::class, 'roles'], [AuthMiddleware::class]);
$router->post('/admin/roles', [AdminController::class, 'storeRole'], [AuthMiddleware::class]);
$router->post('/admin/roles/{id}/delete', [AdminController::class, 'deleteRole'], [AuthMiddleware::class]);
$router->post('/admin/roles/{id}/permissions', [AdminController::class, 'assignPermission'], [AuthMiddleware::class]);
$router->get('/admin/roles/{roleId}/permissions/{permissionId}/remove', [AdminController::class, 'removePermission'], [AuthMiddleware::class]);
$router->get('/admin/permissions', [AdminController::class, 'permissions'], [AuthMiddleware::class]);
$router->post('/admin/permissions', [AdminController::class, 'storePermission'], [AuthMiddleware::class]);
$router->post('/admin/permissions/{id}/delete', [AdminController::class, 'deletePermission'], [AuthMiddleware::class]);

$router->dispatch(Request::method(), Request::uri());
