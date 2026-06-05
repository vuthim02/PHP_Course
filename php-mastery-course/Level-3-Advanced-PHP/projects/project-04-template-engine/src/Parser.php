<?php

declare(strict_types=1);

namespace TemplateEngine;

class Parser
{
    private array $filters = [];

    public function addFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    public function parse(array $tokens): string
    {
        $php = '';
        $stack = [];

        foreach ($tokens as $token) {
            [$type, $value] = $token;

            switch ($type) {
                case Lexer::TOKEN_TEXT:
                    $php .= '$this->out(' . var_export($value, true) . ');';
                    break;

                case Lexer::TOKEN_VAR:
                    $php .= '$this->out(' . $this->compileExpression($value) . ');';
                    break;

                case Lexer::TOKEN_BLOCK:
                    $php .= $this->compileBlock($value, $stack) . "\n";
                    break;

                case Lexer::TOKEN_COMMENT:
                    break;
            }
        }

        return $php;
    }

    private function compileExpression(string $expr): string
    {
        $expr = trim($expr);

        if (str_contains($expr, '|')) {
            $parts = explode('|', $expr);
            $var = trim(array_shift($parts));
            $code = $this->compileVariable($var);

            foreach ($parts as $filter) {
                $filter = trim($filter);
                if (preg_match('/^(\w+)\((.+)\)$/', $filter, $m)) {
                    $code = "\$this->applyFilter('{$m[1]}', $code, {$m[2]})";
                } else {
                    $code = "\$this->applyFilter('$filter', $code)";
                }
            }

            return $code;
        }

        return $this->compileVariable($expr);
    }

    private function compileVariable(string $var): string
    {
        if (preg_match('/^[a-zA-Z_]\w*\(/', $var)) {
            return "\$this->callFunction($var)";
        }

        if (preg_match('/^[a-zA-Z_]\w*(\.\w+)*$/', $var)) {
            $parts = explode('.', $var);
            $code = '$context';
            foreach ($parts as $part) {
                $code .= "['$part'] ?? null";
            }
            return '(' . $code . ')';
        }

        if (is_numeric($var) || in_array($var, ['true', 'false', 'null', 'TRUE', 'FALSE', 'NULL'], true)) {
            return $var;
        }

        if (preg_match('/^["\']/', $var)) {
            return $var;
        }

        return $var;
    }

    private function compileBlock(string $block, array &$stack): string
    {
        if (preg_match('/^if\s+(.+)$/i', $block, $m)) {
            $stack[] = 'if';
            return 'if(' . $this->compileExpression($m[1]) . '):';
        }

        if (preg_match('/^elseif\s+(.+)$/i', $block, $m)) {
            return 'elseif(' . $this->compileExpression($m[1]) . '):';
        }

        if ($block === 'else') {
            return 'else:';
        }

        if ($block === 'endif') {
            array_pop($stack);
            return 'endif;';
        }

        if (preg_match('/^for\s+(\w+)\s+in\s+(.+)$/i', $block, $m)) {
            $stack[] = 'for';
            $item = '$' . $m[1];
            $iterable = $this->compileExpression($m[2]);
            return "foreach({$iterable} as {$item}):";
        }

        if (preg_match('/^for\s+(\w+)\s*,\s*(\w+)\s+in\s+(.+)$/i', $block, $m)) {
            $stack[] = 'for';
            $key = '$' . $m[1];
            $item = '$' . $m[2];
            $iterable = $this->compileExpression($m[3]);
            return "foreach({$iterable} as {$key} => {$item}):";
        }

        if ($block === 'endfor') {
            array_pop($stack);
            return 'endforeach;';
        }

        if (preg_match('/^extends\s+[\'"](.+)[\'"]$/i', $block, $m)) {
            return '$this->setLayout(' . var_export($m[1], true) . ');';
        }

        if (preg_match('/^block\s+(\w+)$/i', $block, $m)) {
            $stack[] = 'block';
            return '$this->startBlock(' . var_export($m[1], true) . ');';
        }

        if ($block === 'endblock') {
            array_pop($stack);
            return '$this->endBlock();';
        }

        if (preg_match('/^include\s+[\'"](.+)[\'"]$/i', $block, $m)) {
            return '$this->include(' . var_export($m[1], true) . ');';
        }

        if (preg_match('/^set\s+(\w+)\s*=\s*(.+)$/i', $block, $m)) {
            return '$context[' . var_export($m[1], true) . '] = ' . $this->compileExpression($m[2]) . ';';
        }

        return '';
    }
}
