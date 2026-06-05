<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Core\View;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login to access this page.');
            View::redirect('/login');
        }
    }
}
