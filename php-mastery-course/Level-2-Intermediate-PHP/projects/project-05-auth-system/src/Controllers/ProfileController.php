<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\User;
use App\Services\PasswordService;

class ProfileController
{
    private PasswordService $passwordService;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }
        $this->passwordService = new PasswordService();
    }

    public function show(): void
    {
        $user = User::find((int) Session::get('user_id'));
        View::render('profile/show', ['user' => $user]);
    }

    public function update(): void
    {
        $user = User::find((int) Session::get('user_id'));
        if (!$user) {
            Session::flash('error', 'User not found.');
            View::redirect('/login');
        }

        $user->username = Request::post('username', $user->username);
        $user->email = Request::post('email', $user->email);

        if (User::findByEmail($user->email) && User::findByEmail($user->email)->id !== $user->id) {
            Session::flash('error', 'Email already taken.');
            View::redirect('/profile');
        }

        $user->save();
        Session::flash('success', 'Profile updated.');
        View::redirect('/profile');
    }

    public function changePassword(): void
    {
        try {
            $this->passwordService->changePassword(
                (int) Session::get('user_id'),
                Request::post('current_password', ''),
                Request::post('new_password', '')
            );
            Session::flash('success', 'Password changed successfully.');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
        }

        View::redirect('/profile');
    }
}
