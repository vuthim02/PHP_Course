<?php

declare(strict_types=1);

return [
    'base_url' => 'http://localhost:8080',
    'database' => [
        'host' => '127.0.0.1',
        'name' => 'url_shortener',
        'user' => 'root',
        'pass' => '',
        'replica_count' => 2,
    ],
    'redis' => [
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
    ],
    'rate_limit' => [
        'max_tokens' => 100,
        'refill_rate' => 10,
        'refill_interval' => 1,
    ],
    'cache' => [
        'ttl_short' => 3600,
        'ttl_long' => 86400,
    ],
    'worker_id' => 1,
];
