<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);
        if (!$user || !$user->verifyPassword($password)) {
            Session::flash('error', 'Invalid credentials.');
            View::redirect('/login');
        }

        Session::set('user_id', $user->id);
        Session::flash('success', 'Welcome back!');
        View::redirect('/boards');
    }

    public function showRegister(): void
    {
        View::render('auth/register');
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (User::findByEmail($email)) {
            Session::flash('error', 'Email already registered.');
            View::redirect('/register');
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->save();

        Session::flash('success', 'Registered successfully. Please login.');
        View::redirect('/login');
    }

    public function logout(): void
    {
        Session::destroy();
        View::redirect('/');
    }
}
