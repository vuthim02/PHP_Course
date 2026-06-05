<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\Link;
use App\Models\Click;

class StatsController
{
    public function show(int $id): void
    {
        if (!Session::has('user_id')) {
            View::redirect('/login');
        }

        $link = Link::find($id);
        if (!$link || $link->user_id !== (int) Session::get('user_id')) {
            Session::flash('error', 'Not found.');
            View::redirect('/links');
        }

        $clicks = $link->clickStats();
        $referers = Click::refererStats($id);

        View::render('links/stats', [
            'link'     => $link,
            'clicks'   => $clicks,
            'referers' => $referers,
        ]);
    }
}
