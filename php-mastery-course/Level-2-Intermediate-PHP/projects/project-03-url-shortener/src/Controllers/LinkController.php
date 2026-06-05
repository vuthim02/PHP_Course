<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Models\Link;
use App\Models\User;

class LinkController
{
    private function requireAuth(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login.');
            View::redirect('/login');
        }
    }

    public function index(): void
    {
        $this->requireAuth();
        $links = Link::findByUser((int) Session::get('user_id'));
        View::render('links/list', ['links' => $links]);
    }

    public function create(): void
    {
        $this->requireAuth();
        View::render('links/create');
    }

    public function store(): void
    {
        $this->requireAuth();
        $longUrl = Request::post('long_url');
        $customCode = Request::post('custom_code');
        $expiresAt = Request::post('expires_at') ?: null;

        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
            Session::flash('error', 'Invalid URL.');
            View::redirect('/links/create');
        }

        $link = new Link();
        $link->long_url = $longUrl;
        $link->short_code = $customCode ?: Link::generateShortCode();
        $link->user_id = (int) Session::get('user_id');
        $link->expires_at = $expiresAt;

        if ($customCode && Link::findByCode($customCode)) {
            Session::flash('error', 'Custom code already taken.');
            View::redirect('/links/create');
        }

        $link->save();
        Session::flash('success', 'Short link created!');
        View::redirect('/links');
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();
        $link = Link::find($id);
        if ($link && $link->user_id === (int) Session::get('user_id')) {
            $link->delete();
            Session::flash('success', 'Link deleted.');
        }
        View::redirect('/links');
    }
}
