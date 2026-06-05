<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public static function start(): void { if (session_status() === PHP_SESSION_NONE) session_start(); }
    public static function set(string $k, mixed $v): void { $_SESSION[$k] = $v; }
    public static function get(string $k, mixed $d = null): mixed { return $_SESSION[$k] ?? $d; }
    public static function has(string $k): bool { return isset($_SESSION[$k]); }
    public static function remove(string $k): void { unset($_SESSION[$k]); }
    public static function destroy(): void { session_destroy(); }
    public static function regenerate(): void { session_regenerate_id(true); }
    public static function flash(string $k, ?string $v = null): ?string
    {
        if ($v !== null) { $_SESSION['_flash'][$k] = $v; return null; }
        $val = $_SESSION['_flash'][$k] ?? null; unset($_SESSION['_flash'][$k]); return $val;
    }
}
