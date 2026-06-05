<?php

declare(strict_types=1);

namespace UserService\Tests\Services;

use PHPUnit\Framework\TestCase;
use UserService\Services\UserService;

/**
 * Unit tests for UserService.
 * Uses SQLite in-memory database and mock Redis for testing.
 */
final class UserServiceTest extends TestCase
{
    private \PDO $pdo;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->exec('
            CREATE TABLE users (
                id VARCHAR(64) PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                last_login_at DATETIME DEFAULT NULL,
                is_active INTEGER DEFAULT 1
            )
        ');
        $this->pdo->exec('
            CREATE TABLE profiles (
                user_id VARCHAR(64) PRIMARY KEY,
                bio TEXT DEFAULT NULL,
                avatar_url VARCHAR(500) DEFAULT NULL,
                company VARCHAR(255) DEFAULT NULL,
                location VARCHAR(255) DEFAULT NULL,
                website VARCHAR(500) DEFAULT NULL,
                social_links TEXT DEFAULT NULL
            )
        ');

        // Mock Redis
        $redis = $this->createMock(\Redis::class);
        $redis->method('get')->willReturn(false);
        $redis->method('setex')->willReturn(true);
        $redis->method('del')->willReturn(true);

        $this->userService = new UserService($this->pdo, $redis);
    }

    public function testCreateUser(): void
    {
        $user = $this->userService->create([
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User',
        ]);

        $this->assertNotEmpty($user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('Test User', $user->name);
    }

    public function testGetUser(): void
    {
        $created = $this->userService->create([
            'email' => 'get@example.com',
            'password' => 'password123',
            'name' => 'Get Test',
        ]);

        $retrieved = $this->userService->get($created->id);
        $this->assertNotNull($retrieved);
        $this->assertEquals($created->id, $retrieved->id);
    }

    public function testListUsers(): void
    {
        $this->userService->create(['email' => 'a@test.com', 'password' => 'pass1234', 'name' => 'A']);
        $this->userService->create(['email' => 'b@test.com', 'password' => 'pass1234', 'name' => 'B']);

        $result = $this->userService->list(1, 10);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(2, $result['pagination']['total']);
    }

    public function testDeleteUser(): void
    {
        $user = $this->userService->create([
            'email' => 'delete@test.com',
            'password' => 'password123',
            'name' => 'Delete Me',
        ]);

        $this->userService->delete($user->id);
        $this->assertNull($this->userService->get($user->id));
    }
}
