<?php

declare(strict_types=1);

namespace DebuggerProfiler;

class Dump
{
    public static function dump(mixed $value, int $depth = 0, int $maxDepth = 5): string
    {
        if ($depth > $maxDepth) {
            return '*MAX DEPTH*';
        }

        if ($value === null) {
            return self::highlight('null', 'keyword');
        }

        if (is_bool($value)) {
            return self::highlight($value ? 'true' : 'false', 'bool');
        }

        if (is_string($value)) {
            $str = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            return self::highlight("'" . $str . "'", 'string');
        }

        if (is_int($value) || is_float($value)) {
            return self::highlight((string) $value, 'number');
        }

        if (is_array($value)) {
            return self::dumpArray($value, $depth, $maxDepth);
        }

        if (is_object($value)) {
            return self::dumpObject($value, $depth, $maxDepth);
        }

        if (is_resource($value)) {
            return self::highlight('resource(' . get_resource_type($value) . ')', 'resource');
        }

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private static function dumpArray(array $arr, int $depth, int $maxDepth): string
    {
        $indent = str_repeat('  ', $depth + 1);
        $html   = "array:" . count($arr) . " [\n";

        foreach ($arr as $key => $val) {
            $k = is_string($key)
                ? self::highlight("'$key'", 'string')
                : self::highlight((string) $key, 'number');
            $html .= "$indent$k => " . self::dump($val, $depth + 1, $maxDepth) . "\n";
        }

        $html .= str_repeat('  ', $depth) . ']';
        return $html;
    }

    private static function dumpObject(object $obj, int $depth, int $maxDepth): string
    {
        $ref  = new \ReflectionObject($obj);
        $name = $ref->getName();
        $html = self::highlight($name, 'class') . " {\n";
        $indent = str_repeat('  ', $depth + 1);

        foreach ($ref->getProperties() as $prop) {
            $prop->setAccessible(true);
            $modifiers = \Reflection::getModifierNames($prop->getModifiers());
            $modStr    = implode(' ', $modifiers);
            $val       = self::dump($prop->getValue($obj), $depth + 1, $maxDepth);
            $pname     = self::highlight("\${$prop->getName()}", 'property');
            $html .= "$indent$modStr $pname = $val\n";
        }

        $html .= str_repeat('  ', $depth) . '}';
        return $html;
    }

    private static function highlight(string $text, string $type): string
    {
        $colors = [
            'keyword'  => '#7B1FA2',
            'string'   => '#2E7D32',
            'number'   => '#1565C0',
            'bool'     => '#E65100',
            'class'    => '#C62828',
            'property' => '#6A1B9A',
            'resource' => '#00838F',
        ];

        $color = $colors[$type] ?? '#333';
        return sprintf('<span style="color:%s">%s</span>', $color, $text);
    }
}
