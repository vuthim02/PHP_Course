<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;

class AuthService
{
    public function register(array $data): User
    {
        if (User::findByEmail($data['email'])) {
            throw new \RuntimeException('Email already registered.');
        }

        if (strlen($data['password'] ?? '') < 8) {
            throw new \RuntimeException('Password must be at least 8 characters.');
        }

        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->save();

        return $user;
    }

    public function login(string $email, string $password): User
    {
        $user = User::findByEmail($email);

        if (!$user) {
            throw new \RuntimeException('Invalid email or password.');
        }

        if (!$user->verifyPassword($password)) {
            throw new \RuntimeException('Invalid email or password.');
        }

        if (!$user->isActive()) {
            throw new \RuntimeException('Account is deactivated.');
        }

        Session::set('user_id', $user->id);
        Session::set('user_role', $user->role);
        Session::regenerate();

        return $user;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function getCurrentUser(): ?User
    {
        if (!Session::has('user_id')) {
            return null;
        }
        return User::find((int) Session::get('user_id'));
    }

    public function isLoggedIn(): bool
    {
        return Session::has('user_id');
    }

    public function hasRole(string $role): bool
    {
        return Session::get('user_role') === $role;
    }
}
