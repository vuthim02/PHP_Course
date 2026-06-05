<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $t, array $d = [], string $l = 'layout'): void
    {
        extract($d);
        ob_start();
        require __DIR__ . "/../../templates/{$t}.php";
        $c = ob_get_clean();
        if ($l) require __DIR__ . "/../../templates/{$l}.php";
        else echo $c;
    }

    public static function redirect(string $u): void
    {
        header("Location: {$u}"); exit;
    }

    public static function json(mixed $d, int $s = 200): void
    {
        http_response_code($s);
        header('Content-Type: application/json');
        echo json_encode($d);
        exit;
    }
}
