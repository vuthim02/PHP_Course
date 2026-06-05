<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// ─── Boot ───────────────────────────────────────────────────────────
// Connection pooling via persistent PDO connections
$primaryDsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $_ENV['DB_HOST'] ?? '127.0.0.1',
    $_ENV['DB_NAME'] ?? 'url_shortener'
);

$primaryPdo = new \PDO(
    $primaryDsn,
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_PERSISTENT         => true,
        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
    ]
);

// Read replica connections (simulated — same host, different user/flow)
$replicas = [];
for ($i = 0; $i < ($_ENV['DB_REPLICA_COUNT'] ?? 2); $i++) {
    $replicas[] = new \PDO(
        $primaryDsn,
        $_ENV['DB_USER'] ?? 'root',
        $_ENV['DB_PASS'] ?? '',
        [
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_PERSISTENT => true,
        ]
    );
}

// Redis client
$redis = new \Predis\Client([
    'scheme' => $_ENV['REDIS_SCHEME'] ?? 'tcp',
    'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
    'port'   => $_ENV['REDIS_PORT'] ?? 6379,
]);

// ─── Dependency Wiring ─────────────────────────────────────────────
use UrlShortener\Cache\RedisCache;
use UrlShortener\Queue\RedisQueue;
use UrlShortener\Service\IdGenerator;
use UrlShortener\Service\UrlService;
use UrlShortener\Service\AnalyticsService;
use UrlShortener\Service\BloomFilter;
use UrlShortener\Repository\UrlRepository;
use UrlShortener\Repository\AnalyticsRepository;
use UrlShortener\Middleware\RateLimitMiddleware;
use UrlShortener\Middleware\CacheMiddleware;
use UrlShortener\Controller\ShortenController;
use UrlShortener\Controller\RedirectController;

$cache      = new RedisCache($redis);
$queue      = new RedisQueue($redis);
$bloom      = new BloomFilter($redis);
$idGen      = new IdGenerator((int) ($_ENV['WORKER_ID'] ?? 1));
$urlRepo    = new UrlRepository($primaryPdo, $replicas);
$analyticsRepo = new AnalyticsRepository($primaryPdo);
$rateLimiter = new RateLimitMiddleware($redis);
$cacheMW    = new CacheMiddleware($cache);

$urlService = new UrlService($urlRepo, $cache, $idGen, $bloom);
$analytics  = new AnalyticsService($queue, $analyticsRepo, $cache);

// ─── Routing ───────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Enable CORS for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// POST /api/shorten
if ($method === 'POST' && $path === '/api/shorten') {
    $controller = new ShortenController($urlService, $rateLimiter);
    $controller();
    exit;
}

// GET /<short_code> — redirect
if ($method === 'GET' && preg_match('#^/([a-zA-Z0-9_-]{3,})$#', $path, $matches)) {
    $controller = new RedirectController($urlService, $analytics, $cacheMW);
    $controller($matches[1]);
    exit;
}

// GET /api/stats/<short_code>
if ($method === 'GET' && preg_match('#^/api/stats/([a-zA-Z0-9_-]+)$#', $path, $matches)) {
    $count = $analytics->getClickCount($matches[1]);
    $topReferrers = $analytics->getTopReferrers($matches[1]);
    echo json_encode([
        'short_code' => $matches[1],
        'total_clicks' => $count,
        'top_referrers' => $topReferrers,
    ]);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not found']);
