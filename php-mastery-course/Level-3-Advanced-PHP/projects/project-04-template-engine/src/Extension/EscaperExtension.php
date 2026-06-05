<?php

declare(strict_types=1);

namespace TemplateEngine\Extension;

class EscaperExtension implements ExtensionInterface
{
    public function getFilters(): array
    {
        return [
            'e'       => [$this, 'escape'],
            'escape'  => [$this, 'escape'],
            'raw'     => [$this, 'raw'],
            'upper'   => 'strtoupper',
            'lower'   => 'strtolower',
            'title'   => 'ucwords',
            'trim'    => 'trim',
            'json'    => 'json_encode',
            'length'  => 'strlen',
            'reverse' => 'strrev',
            'slice'   => [$this, 'slice'],
            'join'    => fn(array $arr, string $glue = ',') => implode($glue, $arr),
            'default' => fn(mixed $val, mixed $default) => $val !== null && $val !== '' ? $val : $default,
        ];
    }

    public function getFunctions(): array
    {
        return [
            'dump'    => fn(mixed $var) => print_r($var, true),
            'now'     => fn(string $format = 'Y-m-d H:i:s') => date($format),
            'asset'   => fn(string $path) => '/assets/' . ltrim($path, '/'),
        ];
    }

    public function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function raw(mixed $value): string
    {
        return (string) $value;
    }

    public function slice(string $value, int $start, ?int $length = null): string
    {
        return substr($value, $start, $length);
    }
}
