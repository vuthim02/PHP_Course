<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\CorsMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Router();

$router->addGlobalMiddleware([CorsMiddleware::class, 'process']);
$router->addGlobalMiddleware([RateLimitMiddleware::class, 'process']);

$auth = new AuthMiddleware();

// Health check
$router->get('/health', function () {
    return ['status' => 'ok', 'timestamp' => time()];
});

// API v1 routes
$router->group('/v1', function (Router $router) use ($auth) {
    // Auth routes (no auth required)
    $router->post('/auth/register', ['App\Controllers\AuthController', 'register']);
    $router->post('/auth/login', ['App\Controllers\AuthController', 'login']);
    $router->post('/auth/refresh', ['App\Controllers\AuthController', 'refresh']);

    // Auth routes (auth required)
    $router->get('/auth/me', ['App\Controllers\AuthController', 'me'], [[AuthMiddleware::class, 'process']]);

    // Book routes
    $router->get('/books', ['App\Controllers\BookController', 'index']);
    $router->get('/books/{id}', ['App\Controllers\BookController', 'show']);
    $router->post('/books', ['App\Controllers\BookController', 'store'], [[AuthMiddleware::class, 'process']]);
    $router->put('/books/{id}', ['App\Controllers\BookController', 'update'], [[AuthMiddleware::class, 'process']]);
    $router->delete('/books/{id}', ['App\Controllers\BookController', 'destroy'], [[AuthMiddleware::class, 'process']]);

    // Author routes
    $router->get('/authors', ['App\Controllers\AuthorController', 'index']);
    $router->get('/authors/{id}', ['App\Controllers\AuthorController', 'show']);
    $router->post('/authors', ['App\Controllers\AuthorController', 'store'], [[AuthMiddleware::class, 'process']]);
    $router->put('/authors/{id}', ['App\Controllers\AuthorController', 'update'], [[AuthMiddleware::class, 'process']]);
    $router->delete('/authors/{id}', ['App\Controllers\AuthorController', 'destroy'], [[AuthMiddleware::class, 'process']]);

    // Category routes
    $router->get('/categories', ['App\Controllers\CategoryController', 'index']);
    $router->get('/categories/{id}', ['App\Controllers\CategoryController', 'show']);
    $router->post('/categories', ['App\Controllers\CategoryController', 'store'], [[AuthMiddleware::class, 'process']]);
    $router->put('/categories/{id}', ['App\Controllers\CategoryController', 'update'], [[AuthMiddleware::class, 'process']]);
    $router->delete('/categories/{id}', ['App\Controllers\CategoryController', 'destroy'], [[AuthMiddleware::class, 'process']]);

    // Review routes (nested under books)
    $router->get('/books/{id}/reviews', ['App\Controllers\ReviewController', 'index']);
    $router->post('/books/{id}/reviews', ['App\Controllers\ReviewController', 'store'], [[AuthMiddleware::class, 'process']]);
    $router->delete('/reviews/{id}', ['App\Controllers\ReviewController', 'destroy'], [[AuthMiddleware::class, 'process']]);
});

// OpenAPI docs
$router->get('/docs', function () {
    $spec = json_decode(file_get_contents(__DIR__ . '/docs/openapi.json'), true);
    return Response::json($spec);
});

$router->get('/', function () {
    return Response::json([
        'name' => 'Bookstore API',
        'version' => '1.0.0',
        'docs' => '/docs',
        'health' => '/health',
    ]);
});

$request = new Request();

try {
    $response = $router->resolve($request);
} catch (\Exception $e) {
    $response = Response::error(
        $_ENV['APP_ENV'] === 'development' ? $e->getMessage() : 'Internal Server Error',
        500
    );
}

$response->send();
