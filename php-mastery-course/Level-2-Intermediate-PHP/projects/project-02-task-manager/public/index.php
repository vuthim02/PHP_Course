<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\Request;
use App\Controllers\AuthController;
use App\Controllers\BoardController;
use App\Controllers\CardController;

Session::start();

$router = new Router();

$router->get('/', function() { \App\Core\View::redirect('/boards'); });
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/boards', [BoardController::class, 'index']);
$router->post('/boards', [BoardController::class, 'store']);
$router->get('/boards/{id}', [BoardController::class, 'show']);
$router->post('/boards/{id}/delete', [BoardController::class, 'destroy']);

$router->post('/cards', [CardController::class, 'store']);
$router->post('/cards/{id}/update', [CardController::class, 'update']);
$router->post('/cards/{id}/move', [CardController::class, 'move']);
$router->post('/cards/{id}/delete', [CardController::class, 'destroy']);

$router->dispatch(Request::method(), Request::uri());
