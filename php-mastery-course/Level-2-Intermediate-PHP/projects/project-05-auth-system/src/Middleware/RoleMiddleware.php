<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Core\View;
use App\Models\User;

class RoleMiddleware
{
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    public function handle(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }

        $user = User::find((int) Session::get('user_id'));
        if (!$user || !in_array($user->role, $this->roles, true)) {
            http_response_code(403);
            Session::flash('error', 'You do not have permission to access this page.');
            View::redirect('/');
        }
    }
}
