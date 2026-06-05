<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    public ?int $id = null;
    public string $username;
    public string $email;
    public string $password;
    public string $role = 'user';
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM users WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM users WHERE email = ?', [$email]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByUsername(string $username): ?self
    {
        $db = Database::getInstance();
        $data = $db->fetch('SELECT * FROM users WHERE username = ?', [$username]);
        return $data ? self::hydrate($data) : null;
    }

    public static function all(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll('SELECT * FROM users ORDER BY created_at DESC');
        return array_map(fn($row) => self::hydrate($row), $rows);
    }

    public function save(): int
    {
        $db = Database::getInstance();

        if ($this->id) {
            $db->update('users', [
                'username' => $this->username,
                'email'    => $this->email,
                'password' => $this->password,
                'role'     => $this->role,
            ], 'id = ?', [$this->id]);
            return $this->id;
        }

        return $db->insert('users', [
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
            'role'     => $this->role,
        ]);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    private static function hydrate(object $data): self
    {
        $user = new self();
        $user->id = (int) $data->id;
        $user->username = $data->username;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->role = $data->role;
        $user->created_at = $data->created_at ?? null;
        return $user;
    }
}
