<?php

declare(strict_types=1);

/**
 * User Microservice — Front Controller
 *
 * All HTTP requests are routed through this file.
 * Sets up autoloading, environment, routing, and middleware pipeline.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// ──────────────────────────────────────────────
// Bootstrap
// ──────────────────────────────────────────────

$config = require __DIR__ . '/../config/database.php';

// PDO connection
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

// Redis connection
$redis = new Redis();
$redis->connect(
    $_ENV['REDIS_HOST'] ?? 'redis',
    (int) ($_ENV['REDIS_PORT'] ?? 6379)
);

// ──────────────────────────────────────────────
// Services
// ──────────────────────────────────────────────

$userService = new \UserService\Services\UserService($pdo, $redis);
$authService = new \UserService\Services\AuthService($userService, $redis);

// ──────────────────────────────────────────────
// Middleware & Routing
// ──────────────────────────────────────────────

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// CORS middleware
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ──────────────────────────────────────────────
// Routes
// ──────────────────────────────────────────────

try {
    // Health check
    if ($uri === '/api/health' && $method === 'GET') {
        echo json_encode([
            'status' => 'healthy',
            'service' => 'user-service',
            'timestamp' => date('c'),
            'database' => $pdo->query('SELECT 1') ? 'connected' : 'disconnected',
        ]);
        exit;
    }

    // Metrics (Prometheus)
    if ($uri === '/api/metrics' && $method === 'GET') {
        header('Content-Type: text/plain');
        echo "# HELP user_service_requests_total Total requests\n";
        echo "# TYPE user_service_requests_total counter\n";
        echo "user_service_requests_total{endpoint=\"{$uri}\"} 1\n";
        echo "# HELP user_service_users_total Total registered users\n";
        echo "# TYPE user_service_users_total gauge\n";
        echo "user_service_users_total " . $userService->count() . "\n";
        exit;
    }

    // Auth routes
    if ($uri === '/api/auth/register' && $method === 'POST') {
        $result = $authService->register(
            $body['email'] ?? '',
            $body['password'] ?? '',
            $body['name'] ?? ''
        );
        http_response_code(201);
        echo json_encode($result);
        exit;
    }

    if ($uri === '/api/auth/login' && $method === 'POST') {
        $result = $authService->login(
            $body['email'] ?? '',
            $body['password'] ?? ''
        );
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
        exit;
    }

    // User CRUD routes
    if (preg_match('#^/api/users/([a-f0-9\-]+)/profile$#', $uri, $matches)) {
        if ($method === 'GET') {
            $profile = $userService->getProfile($matches[1]);
            echo $profile ? json_encode($profile) : json_encode(['error' => 'Not found']);
            exit;
        }
        if ($method === 'PUT') {
            $updated = $userService->updateProfile($matches[1], $body);
            echo json_encode($updated);
            exit;
        }
    }

    if ($uri === '/api/users' && $method === 'GET') {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
        echo json_encode($userService->list($page, $perPage));
        exit;
    }

    if ($uri === '/api/users' && $method === 'POST') {
        $user = $userService->create($body);
        http_response_code(201);
        echo json_encode($user);
        exit;
    }

    if (preg_match('#^/api/users/([a-f0-9\-]+)$#', $uri, $matches)) {
        $id = $matches[1];

        if ($method === 'GET') {
            $user = $userService->get($id);
            echo $user ? json_encode($user) : json_encode(['error' => 'Not found']);
            exit;
        }

        if ($method === 'PUT') {
            $user = $userService->update($id, $body);
            echo json_encode($user);
            exit;
        }

        if ($method === 'DELETE') {
            $userService->delete($id);
            http_response_code(204);
            exit;
        }
    }

    // 404 fallback
    http_response_code(404);
    echo json_encode(['error' => 'Not found', 'path' => $uri]);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $_ENV['APP_ENV'] === 'development' ? $e->getMessage() : 'An error occurred',
    ]);
}
