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
    public string $status = 'active';
    public ?string $remember_token = null;
    public ?string $reset_token = null;
    public ?string $reset_token_expires = null;
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM users WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM users WHERE email = ?', [$email]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByRememberToken(string $token): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM users WHERE remember_token = ?', [$token]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByResetToken(string $token): ?self
    {
        $d = Database::getInstance()->fetch(
            'SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()', [$token]
        );
        return $d ? self::hydrate($d) : null;
    }

    public static function all(): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM users ORDER BY created_at DESC')
        );
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'status' => $this->status,
            'remember_token' => $this->remember_token,
            'reset_token' => $this->reset_token,
            'reset_token_expires' => $this->reset_token_expires,
        ];
        if ($this->id) { $db->update('users', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('users', $d);
    }

    public function verifyPassword(string $pwd): bool { return password_verify($pwd, $this->password); }

    public function hasRole(string $role): bool { return $this->role === $role; }

    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function isActive(): bool { return $this->status === 'active'; }

    public function generateRememberToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->remember_token = password_hash($token, PASSWORD_DEFAULT);
        $this->save();
        return $token;
    }

    public function generateResetToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->reset_token = hash('sha256', $token);
        $this->reset_token_expires = date('Y-m-d H:i:s', time() + 3600);
        $this->save();
        return $token;
    }

    public function clearResetToken(): void
    {
        $this->reset_token = null;
        $this->reset_token_expires = null;
        $this->save();
    }

    public function roles(): array
    {
        $rows = Database::getInstance()->fetchAll(
            'SELECT r.* FROM roles r
             JOIN user_roles ur ON r.id = ur.role_id
             WHERE ur.user_id = ?', [$this->id]
        );
        return array_map(fn($r) => Role::hydrate($r), $rows);
    }

    public function hasPermission(string $permission): bool
    {
        $db = Database::getInstance();
        $result = $db->fetch(
            'SELECT 1 FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN user_roles ur ON rp.role_id = ur.role_id
             WHERE ur.user_id = ? AND p.name = ?
             UNION
             SELECT 1 FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = (SELECT id FROM roles WHERE name = ?)
             AND p.name = ?',
            [$this->id, $permission, $this->role, $permission]
        );
        return $result !== null;
    }

    private static function hydrate(object $d): self
    {
        $u = new self();
        $u->id = (int) $d->id;
        $u->username = $d->username;
        $u->email = $d->email;
        $u->password = $d->password;
        $u->role = $d->role;
        $u->status = $d->status ?? 'active';
        $u->remember_token = $d->remember_token ?? null;
        $u->reset_token = $d->reset_token ?? null;
        $u->reset_token_expires = $d->reset_token_expires ?? null;
        $u->created_at = $d->created_at ?? null;
        return $u;
    }
}
