<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login');
    }

    public function login(): void
    {
        $email = Request::post('email');
        $password = Request::post('password');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            View::redirect('/login');
        }

        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            Session::flash('error', 'Invalid email or password.');
            View::redirect('/login');
        }

        Session::set('user_id', $user->id);
        Session::set('user_role', $user->role);
        Session::flash('success', 'Welcome back, ' . $user->username . '!');

        if ($user->isAdmin()) {
            View::redirect('/admin');
        }
        View::redirect('/');
    }

    public function showRegister(): void
    {
        View::render('auth/register');
    }

    public function register(): void
    {
        $username = Request::post('username');
        $email = Request::post('email');
        $password = Request::post('password');
        $confirmPassword = Request::post('confirm_password');

        if (empty($username) || empty($email) || empty($password)) {
            Session::flash('error', 'All fields are required.');
            View::redirect('/register');
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'Passwords do not match.');
            View::redirect('/register');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            View::redirect('/register');
        }

        if (User::findByEmail($email)) {
            Session::flash('error', 'Email already registered.');
            View::redirect('/register');
        }

        if (User::findByUsername($username)) {
            Session::flash('error', 'Username already taken.');
            View::redirect('/register');
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $user->save();
            Session::flash('success', 'Registration successful. Please login.');
            View::redirect('/login');
        } catch (\Exception $e) {
            Session::flash('error', 'Registration failed. Please try again.');
            View::redirect('/register');
        }
    }

    public function logout(): void
    {
        Session::destroy();
        View::redirect('/');
    }
}
