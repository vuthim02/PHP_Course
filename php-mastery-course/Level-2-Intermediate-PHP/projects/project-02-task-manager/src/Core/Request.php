<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function uri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function post(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $_POST;
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $_GET;
        return $_GET[$key] ?? $default;
    }
}
