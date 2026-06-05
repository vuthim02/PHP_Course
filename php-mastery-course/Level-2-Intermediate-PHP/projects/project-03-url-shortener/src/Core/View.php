<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], string $layout = 'layout'): void
    {
        extract($data);
        ob_start();
        require __DIR__ . "/../../templates/{$template}.php";
        $content = ob_get_clean();
        if ($layout) require __DIR__ . "/../../templates/{$layout}.php";
        else echo $content;
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
