<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class AuthService
{
    private Database $db;
    private string $jwtSecret;
    private string $jwtAlgorithm = 'HS256';
    private int $jwtExpiry;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'change-me';
        $this->jwtExpiry = (int) ($_ENV['JWT_EXPIRY'] ?? 3600);
    }

    public function register(string $name, string $email, string $password): array
    {
        $existing = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            throw new RuntimeException('Email already registered');
        }

        $userId = $this->db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $user = $this->db->fetch("SELECT id, name, email, created_at FROM users WHERE id = ?", [$userId]);
        $token = $this->generateToken($userId);

        return ['user' => $user, 'token' => $token];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new RuntimeException('Invalid credentials');
        }

        $token = $this->generateToken((int) $user['id']);

        return [
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'created_at' => $user['created_at'],
            ],
            'token' => $token,
        ];
    }

    public function validateToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->jwtSecret, $this->jwtAlgorithm));
        } catch (\Exception $e) {
            throw new RuntimeException('Invalid or expired token');
        }
    }

    public function generateToken(int $userId): string
    {
        $payload = [
            'iss' => 'graphql-blog',
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $this->jwtExpiry,
        ];

        return JWT::encode($payload, $this->jwtSecret, $this->jwtAlgorithm);
    }

    public function getUserFromToken(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        try {
            $decoded = $this->validateToken($token);
            return $this->db->fetch("SELECT id, name, email, created_at FROM users WHERE id = ?", [$decoded->sub]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
