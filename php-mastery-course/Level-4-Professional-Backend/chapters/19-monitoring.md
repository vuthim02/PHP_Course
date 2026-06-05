# Chapter 19: Monitoring and Logging

## Learning Objectives

- Implement structured logging
- Set up error tracking (Sentry)
- Configure APM monitoring
- Build health check endpoints

---

## 19.1 Structured Logging

```php
<?php
namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\UidProcessor;

class LogManager
{
    private static ?Logger $instance = null;

    public static function getLogger(string $channel = 'app'): Logger
    {
        if (self::$instance === null) {
            self::$instance = self::createLogger($channel);
        }
        return self::$instance;
    }

    private static function createLogger(string $channel): Logger
    {
        $logger = new Logger($channel);

        // JSON formatter for structured logging
        $jsonFormatter = new JsonFormatter();

        // Main log handler (rotating, 30 days retention)
        $mainHandler = new RotatingFileHandler(
            __DIR__ . '/../../storage/logs/app.log',
            30,
            Logger::DEBUG
        );
        $mainHandler->setFormatter($jsonFormatter);
        $logger->pushHandler($mainHandler);

        // Error log handler (separate file)
        $errorHandler = new RotatingFileHandler(
            __DIR__ . '/../../storage/logs/error.log',
            30,
            Logger::ERROR
        );
        $errorHandler->setFormatter($jsonFormatter);
        $logger->pushHandler($errorHandler);

        // Add processors for context
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(new UidProcessor());

        return $logger;
    }
}

// Usage
$logger = LogManager::getLogger();
$logger->info('User registered', [
    'user_id' => 123,
    'email' => 'user@example.com',
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

// Output in JSON for easy parsing by log aggregation tools
// {"message":"User registered","context":{"user_id":123,"email":"user@example.com","ip":"192.168.1.1"},"level":200,"level_name":"INFO","channel":"app","extra":{"uid":"abc123",...},"datetime":"2024-01-15T10:30:00+00:00"}
```

---

## 19.2 Health Check Endpoint

```php
<?php
namespace App\Monitoring;

class HealthCheck
{
    private array $checks = [];

    public function addCheck(string $name, callable $check): void
    {
        $this->checks[$name] = $check;
    }

    public function run(): never
    {
        $status = 'healthy';
        $results = [];

        foreach ($this->checks as $name => $check) {
            try {
                $result = $check();
                $results[$name] = [
                    'status' => $result ? 'pass' : 'fail',
                    'time' => hrtime(true),
                ];
                if (!$result) {
                    $status = 'degraded';
                }
            } catch (\Throwable $e) {
                $results[$name] = [
                    'status' => 'fail',
                    'error' => $e->getMessage(),
                ];
                $status = 'unhealthy';
            }
        }

        $httpStatus = match ($status) {
            'healthy' => 200,
            'degraded' => 200,
            'unhealthy' => 503,
        };

        http_response_code($httpStatus);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'timestamp' => date('c'),
            'checks' => $results,
        ]);
        exit;
    }
}

// Register checks
$health = new HealthCheck();

$health->addCheck('database', function () {
    $pdo = new PDO($_ENV['DATABASE_DSN'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASS']);
    $pdo->query('SELECT 1');
    return true;
});

$health->addCheck('redis', function () {
    $redis = new Redis();
    $redis->connect($_ENV['REDIS_HOST'], (int)$_ENV['REDIS_PORT']);
    return $redis->ping();
});

$health->addCheck('disk_space', function () {
    $free = disk_free_space('/');
    return $free > 500 * 1024 * 1024; // 500MB minimum
});

$health->addCheck('queue', function () {
    // Check queue worker is running
    $queue = new QueueService();
    return $queue->isWorkerActive();
});

$health->run();
```

---

## 19.3 Exercises

1. Implement structured JSON logging with Monolog
2. Integrate Sentry for error tracking
3. Build comprehensive health check endpoints
4. Set up Grafana dashboards for PHP application metrics

---

## Further Reading

- **Doc:** [Monolog](https://github.com/Seldaek/monolog)
- **Doc:** [Sentry PHP](https://docs.sentry.io/platforms/php/)
- **Tool:** [Prometheus + Grafana](https://prometheus.io/)
