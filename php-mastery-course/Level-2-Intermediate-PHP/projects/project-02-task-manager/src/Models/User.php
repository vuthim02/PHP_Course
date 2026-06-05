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
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $data = Database::getInstance()->fetch('SELECT * FROM users WHERE id = ?', [$id]);
        return $data ? self::hydrate($data) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $data = Database::getInstance()->fetch('SELECT * FROM users WHERE email = ?', [$email]);
        return $data ? self::hydrate($data) : null;
    }

    public function save(): int
    {
        $db = Database::getInstance();
        if ($this->id) {
            $db->update('users', ['username' => $this->username, 'email' => $this->email, 'password' => $this->password], 'id = ?', [$this->id]);
            return $this->id;
        }
        return $db->insert('users', ['username' => $this->username, 'email' => $this->email, 'password' => $this->password]);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    private static function hydrate(object $data): self
    {
        $u = new self();
        $u->id = (int) $data->id;
        $u->username = $data->username;
        $u->email = $data->email;
        $u->password = $data->password;
        $u->created_at = $data->created_at ?? null;
        return $u;
    }
}
