<?php

declare(strict_types=1);

namespace UserService\Services;

/**
 * AuthService
 *
 * Handles user authentication: registration, login, token management.
 * Tokens are stored in Redis for fast lookup and revocation.
 */
final class AuthService
{
    private const TOKEN_PREFIX = 'auth_token:';
    private const TOKEN_TTL = 86400; // 24 hours

    public function __construct(
        private UserService $userService,
        private \Redis $redis
    ) {
    }

    /**
     * Register a new user.
     */
    public function register(string $email, string $password, string $name): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters');
        }

        // Check for existing user
        $existing = $this->findByEmail($email);
        if ($existing) {
            throw new \RuntimeException('Email already registered');
        }

        $user = $this->userService->create([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ]);

        $token = $this->generateToken($user->id);

        return [
            'user' => $user->toPublicArray(),
            'token' => $token,
        ];
    }

    /**
     * Authenticate a user and return a token.
     */
    public function login(string $email, string $password): ?array
    {
        $stmt = $this->getPdo()->prepare(
            'SELECT * FROM users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($password, $row['password_hash'])) {
            return null;
        }

        $user = \UserService\Models\User::fromRow($row);
        $token = $this->generateToken($user->id);

        // Update last login
        $this->getPdo()->prepare(
            'UPDATE users SET last_login_at = :now WHERE id = :id'
        )->execute(['now' => date('c'), 'id' => $user->id]);

        return [
            'user' => $user->toPublicArray(),
            'token' => $token,
        ];
    }

    /**
     * Validate a token and return the user ID.
     */
    public function validateToken(string $token): ?string
    {
        $userId = $this->redis->get(self::TOKEN_PREFIX . $token);
        return $userId !== false ? $userId : null;
    }

    /**
     * Revoke a token (logout).
     */
    public function revokeToken(string $token): void
    {
        $this->redis->del(self::TOKEN_PREFIX . $token);
    }

    /**
     * Find a user by email.
     */
    private function findByEmail(string $email): ?\UserService\Models\User
    {
        $stmt = $this->getPdo()->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ? \UserService\Models\User::fromRow($row) : null;
    }

    /**
     * Generate an auth token and store in Redis.
     */
    private function generateToken(string $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $this->redis->setex(self::TOKEN_PREFIX . $token, self::TOKEN_TTL, $userId);
        return $token;
    }

    private function getPdo(): \PDO
    {
        // Get the PDO instance — in production via DI container
        $ref = new \ReflectionProperty($this->userService, 'pdo');
        $ref->setAccessible(true);
        return $ref->getValue($this->userService);
    }
}
