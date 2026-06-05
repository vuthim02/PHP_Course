<?php

declare(strict_types=1);

/**
 * Database configuration for the User Microservice.
 *
 * Uses environment variables with sensible defaults for Docker Compose.
 * In Kubernetes, these would come from ConfigMaps or Secrets.
 */
return [
    'host' => $_ENV['DB_HOST'] ?? 'mysql',
    'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
    'database' => $_ENV['DB_DATABASE'] ?? 'user_service',
    'username' => $_ENV['DB_USERNAME'] ?? 'user_service',
    'password' => $_ENV['DB_PASSWORD'] ?? 'secret_password',
    'charset' => 'utf8mb4',
];
