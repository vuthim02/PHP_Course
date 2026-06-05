<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Link;

class RedirectController
{
    public function redirect(string $code): void
    {
        $link = Link::findByCode($code);

        if (!$link || $link->isExpired()) {
            http_response_code(404);
            echo 'Link not found or expired.';
            return;
        }

        $link->recordClick();
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $link->long_url);
        exit;
    }
}
