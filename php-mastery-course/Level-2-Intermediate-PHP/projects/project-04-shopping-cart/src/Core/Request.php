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
    public static function get(string $k = null, mixed $d = null): mixed
    {
        return $k === null ? $_GET : ($_GET[$k] ?? $d);
    }
}
