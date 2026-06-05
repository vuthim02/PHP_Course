<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    public static function method(): string { return $_SERVER['REQUEST_METHOD']; }
    public static function uri(): string { return $_SERVER['REQUEST_URI']; }
    public static function post(string $k = null, mixed $d = null): mixed
    {
        return $k === null ? $_POST : ($_POST[$k] ?? $d);
    }
    public static function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    public static function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    public static function referer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }
}
