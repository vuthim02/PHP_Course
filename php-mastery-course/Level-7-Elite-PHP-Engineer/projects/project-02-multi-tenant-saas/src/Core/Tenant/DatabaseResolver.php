<?php

declare(strict_types=1);

namespace MultiTenant\Core\Tenant;

/**
 * Resolves and caches database connections per tenant.
 * Implements connection pooling to avoid creating new PDO instances
 * for every request to the same tenant.
 */
final class DatabaseResolver
{
    /** @var array<string, \PDO> Connection pool */
    private static array $connections = [];

    private readonly string $host;
    private readonly string $user;
    private readonly string $pass;

    public function __construct(
        ?string $host = null,
        ?string $user = null,
        ?string $pass = null
    ) {
        $this->host = $host ?? $_ENV['DB_HOST'] ?? '127.0.0.1';
        $this->user = $user ?? $_ENV['DB_USER'] ?? 'root';
        $this->pass = $pass ?? $_ENV['DB_PASS'] ?? '';
    }

    public function getConnection(Tenant $tenant): \PDO
    {
        $key = $tenant->databaseName;

        if (isset(self::$connections[$key])) {
            return self::$connections[$key];
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $this->host,
            $tenant->databaseName
        );

        $pdo = new \PDO($dsn, $this->user, $this->pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_PERSISTENT => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]);

        self::$connections[$key] = $pdo;

        return $pdo;
    }

    public function createTenantDatabase(Tenant $tenant): void
    {
        $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $this->host);
        $pdo = new \PDO($dsn, $this->user, $this->pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $databaseName = $tenant->databaseName;
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public function dropTenantDatabase(Tenant $tenant): void
    {
        $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $this->host);
        $pdo = new \PDO($dsn, $this->user, $this->pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $databaseName = $tenant->databaseName;
        $pdo->exec("DROP DATABASE IF EXISTS `{$databaseName}`");

        unset(self::$connections[$tenant->databaseName]);
    }

    public static function clearPool(): void
    {
        self::$connections = [];
    }
}
