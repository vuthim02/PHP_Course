<?php

declare(strict_types=1);

/**
 * Database Migration Runner
 *
 * Applies schema migrations to the MySQL database.
 * In production, use a migration tool like Phinx or Doctrine Migrations.
 *
 * Usage: docker-compose exec app php migrations/migrate.php
 */

$config = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$config['database']}`");

    echo "Running migrations...\n";

    // Migration 001: Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(64) PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            INDEX idx_users_email (email),
            INDEX idx_users_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✅ Created users table\n";

    // Migration 002: Create profiles table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS profiles (
            user_id VARCHAR(64) PRIMARY KEY,
            bio TEXT DEFAULT NULL,
            avatar_url VARCHAR(500) DEFAULT NULL,
            company VARCHAR(255) DEFAULT NULL,
            location VARCHAR(255) DEFAULT NULL,
            website VARCHAR(500) DEFAULT NULL,
            social_links JSON DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✅ Created profiles table\n";

    // Migration 003: Create migrations tracking table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "  ✅ Created migrations tracking table\n";

    echo "\nAll migrations completed successfully.\n";

} catch (PDOException $e) {
    echo "Migration failed: {$e->getMessage()}\n";
    exit(1);
}
