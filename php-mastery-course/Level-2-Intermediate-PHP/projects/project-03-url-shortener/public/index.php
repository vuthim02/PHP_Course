<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Controllers\AuthController;
use App\Controllers\LinkController;
use App\Controllers\RedirectController;
use App\Controllers\StatsController;

Session::start();

$router = new Router();

$router->get('/', function() {
    View::render('links/index', [], 'layout');
});

$router->get('/links', [LinkController::class, 'index']);
$router->get('/links/create', [LinkController::class, 'create']);
$router->post('/links', [LinkController::class, 'store']);
$router->post('/links/{id}/delete', [LinkController::class, 'destroy']);
$router->get('/links/{id}/stats', [StatsController::class, 'show']);

$router->get('/{code}', [RedirectController::class, 'redirect']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->dispatch(Request::method(), Request::uri());
