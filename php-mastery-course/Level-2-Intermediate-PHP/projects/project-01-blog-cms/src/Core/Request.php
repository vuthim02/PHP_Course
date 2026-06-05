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
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_POST) || array_key_exists($key, $_GET);
    }

    public static function validate(array $rules): array
    {
        $errors = [];
        $data = [];

        foreach ($rules as $field => $ruleSet) {
            $value = self::post($field);
            $data[$field] = $value;

            foreach (explode('|', $ruleSet) as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = "{$field} is required";
                }

                if (str_starts_with($rule, 'min:') && strlen((string) $value) < (int) substr($rule, 4)) {
                    $errors[$field][] = "{$field} must be at least " . substr($rule, 4) . " characters";
                }

                if (str_starts_with($rule, 'max:') && strlen((string) $value) > (int) substr($rule, 4)) {
                    $errors[$field][] = "{$field} must not exceed " . substr($rule, 4) . " characters";
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} must be a valid email";
                }
            }
        }

        if (!empty($errors)) {
            Session::set('errors', $errors);
            Session::set('old', $data);
            View::redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        return $data;
    }
}
