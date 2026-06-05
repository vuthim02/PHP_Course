<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;

class AuthController
{
    public function showLogin(): void { View::render('auth/login'); }

    public function login(): void
    {
        $user = User::findByEmail($_POST['email'] ?? '');
        if (!$user || !$user->verifyPassword($_POST['password'] ?? '')) {
            Session::flash('error', 'Invalid credentials.');
            View::redirect('/login');
        }
        Session::set('user_id', $user->id);
        Session::flash('success', 'Welcome.');
        View::redirect('/links');
    }

    public function showRegister(): void { View::render('auth/register'); }

    public function register(): void
    {
        if (User::findByEmail($_POST['email'] ?? '')) {
            Session::flash('error', 'Email taken.');
            View::redirect('/register');
        }
        $u = new User();
        $u->username = $_POST['username'] ?? '';
        $u->email = $_POST['email'] ?? '';
        $u->password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $u->save();
        Session::flash('success', 'Registered. Please login.');
        View::redirect('/login');
    }

    public function logout(): void { Session::destroy(); View::redirect('/'); }
}
