<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Services\AuthService;
use App\Services\PasswordService;

class AuthController
{
    private AuthService $auth;
    private PasswordService $passwordService;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->passwordService = new PasswordService();
    }

    public function showLogin(): void
    {
        if ($this->auth->isLoggedIn()) {
            View::redirect('/');
        }
        View::render('auth/login');
    }

    public function login(): void
    {
        try {
            $user = $this->auth->login(
                Request::post('email', ''),
                Request::post('password', '')
            );

            // Remember me
            if (Request::post('remember_me')) {
                $token = $user->generateRememberToken();
                setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
            }

            Session::flash('success', 'Welcome back, ' . $user->username . '!');

            if ($user->isAdmin()) {
                View::redirect('/admin');
            }
            View::redirect('/profile');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            View::redirect('/login');
        }
    }

    public function showRegister(): void
    {
        if ($this->auth->isLoggedIn()) {
            View::redirect('/');
        }
        View::render('auth/register');
    }

    public function register(): void
    {
        try {
            $this->auth->register([
                'username' => Request::post('username', ''),
                'email'    => Request::post('email', ''),
                'password' => Request::post('password', ''),
            ]);
            Session::flash('success', 'Registration successful! Please login.');
            View::redirect('/login');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            View::redirect('/register');
        }
    }

    public function logout(): void
    {
        $this->auth->logout();
        setcookie('remember_token', '', time() - 3600, '/');
        View::redirect('/');
    }

    public function showForgotPassword(): void
    {
        View::render('auth/forgot');
    }

    public function forgotPassword(): void
    {
        try {
            $token = $this->passwordService->requestReset(Request::post('email', ''));
            Session::flash('success', 'Password reset link sent. Token: ' . $token);
            View::redirect('/login');
        } catch (\RuntimeException $e) {
            Session::flash('success', 'If the email exists, a reset link has been sent.');
            View::redirect('/login');
        }
    }

    public function showResetPassword(): void
    {
        View::render('auth/reset', ['token' => Request::get('token', '')]);
    }

    public function resetPassword(): void
    {
        try {
            $this->passwordService->resetPassword(
                Request::post('token', ''),
                Request::post('password', '')
            );
            Session::flash('success', 'Password reset successfully. Please login.');
            View::redirect('/login');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
            View::redirect('/login');
        }
    }
}
