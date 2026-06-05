<?php

declare(strict_types=1);

namespace TemplateEngine;

class Lexer
{
    public const TOKEN_VAR     = 'T_VAR';
    public const TOKEN_BLOCK   = 'T_BLOCK';
    public const TOKEN_COMMENT = 'T_COMMENT';
    public const TOKEN_TEXT    = 'T_TEXT';

    public function tokenize(string $template): array
    {
        $tokens = [];
        $parts  = preg_split(
            '/(\{\{.*?\}\}|{%\s*(.*?)\s*%}|{#.*?#})/s',
            $template,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $i = 0;
        while ($i < count($parts)) {
            $part = $parts[$i];

            if (preg_match('/^\{\{(.*?)\}\}$/s', $part, $m)) {
                $tokens[] = [self::TOKEN_VAR, trim($m[1])];
                $i++;
            } elseif (preg_match('/^{%\s*(.*?)\s*%}$/s', $part, $m)) {
                $tokens[] = [self::TOKEN_BLOCK, trim($m[1])];
                $i += 2; // Skip the inner capture group that follows
            } elseif (preg_match('/^\{#.*?#\}$/s', $part)) {
                $tokens[] = [self::TOKEN_COMMENT, ''];
                $i++;
            } else {
                $tokens[] = [self::TOKEN_TEXT, $part];
                $i++;
            }
        }

        return $tokens;
    }
}
