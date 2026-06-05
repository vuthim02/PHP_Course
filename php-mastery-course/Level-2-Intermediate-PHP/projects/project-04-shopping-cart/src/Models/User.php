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
        $d = Database::getInstance()->fetch('SELECT * FROM users WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM users WHERE email = ?', [$email]);
        return $d ? self::hydrate($d) : null;
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $d = ['username' => $this->username, 'email' => $this->email, 'password' => $this->password];
        if ($this->id) { $db->update('users', $d, 'id = ?', [$this->id]); return $this->id; }
        return $db->insert('users', $d);
    }

    public function verifyPassword(string $pwd): bool { return password_verify($pwd, $this->password); }

    private static function hydrate(object $d): self
    {
        $u = new self();
        $u->id = (int) $d->id; $u->username = $d->username; $u->email = $d->email; $u->password = $d->password; $u->created_at = $d->created_at ?? null;
        return $u;
    }
}
