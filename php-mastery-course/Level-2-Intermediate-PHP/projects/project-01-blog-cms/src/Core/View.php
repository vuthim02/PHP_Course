<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private static string $basePath = __DIR__ . '/../../templates';

    public static function render(string $template, array $data = [], string $layout = 'layout'): void
    {
        extract($data);

        ob_start();
        require self::$basePath . "/{$template}.php";
        $content = ob_get_clean();

        if ($layout) {
            require self::$basePath . "/{$layout}.php";
        } else {
            echo $content;
        }
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
