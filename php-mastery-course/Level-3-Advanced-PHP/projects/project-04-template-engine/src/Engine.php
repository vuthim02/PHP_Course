<?php

declare(strict_types=1);

namespace TemplateEngine;

use TemplateEngine\Cache\FileCache;
use TemplateEngine\Extension\ExtensionInterface;

class Engine
{
    private Lexer $lexer;
    private Parser $parser;
    private FileCache $cache;
    private array $filters = [];
    private array $functions = [];
    private string $templateDir;

    public function __construct(string $templateDir, ?FileCache $cache = null)
    {
        $this->lexer       = new Lexer();
        $this->parser      = new Parser();
        $this->cache       = $cache ?? new FileCache();
        $this->templateDir = rtrim($templateDir, '/');

        $this->addExtension(new Extension\EscaperExtension());
    }

    public function addExtension(ExtensionInterface $extension): void
    {
        foreach ($extension->getFilters() as $name => $callback) {
            $this->addFilter($name, $callback);
        }
        foreach ($extension->getFunctions() as $name => $callback) {
            $this->functions[$name] = $callback;
        }
    }

    public function addFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
        $this->parser->addFilter($name, $callback);
    }

    public function render(string $template, array $context = []): string
    {
        $path = $this->resolveTemplate($template);
        $mtime = filemtime($path);

        if (!$this->cache->has($template, $mtime)) {
            $source = file_get_contents($path);
            $tokens = $this->lexer->tokenize($source);
            $php    = $this->parser->parse($tokens);
            $this->cache->set($template, $php);
        } else {
            $php = $this->cache->get($template);
        }

        $state = new RenderState($context, $this->filters, $this->functions, $this);

        try {
            $state->execute($php);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Template error: {$e->getMessage()}", 0, $e);
        }

        if ($state->layout !== null) {
            $layoutPath = $this->resolveTemplate($state->layout);
            $layoutMtime = filemtime($layoutPath);
            $layoutKey = 'layout:' . $state->layout;

            if (!$this->cache->has($layoutKey, $layoutMtime)) {
                $layoutSource = file_get_contents($layoutPath);
                $layoutTokens = $this->lexer->tokenize($layoutSource);
                $layoutPhp    = $this->parser->parse($layoutTokens);
                $this->cache->set($layoutKey, $layoutPhp);
            } else {
                $layoutPhp = $this->cache->get($layoutKey);
            }

            $layoutState = new RenderState(
                array_merge($context, ['blocks' => $state->blocks]),
                $this->filters,
                $this->functions,
                $this
            );

            try {
                $layoutState->execute($layoutPhp);
            } catch (\Throwable $e) {
                throw new \RuntimeException("Layout error: {$e->getMessage()}", 0, $e);
            }

            return $layoutState->getOutput();
        }

        return $state->getOutput();
    }

    public function renderString(string $source, array $context = []): string
    {
        $tokens = $this->lexer->tokenize($source);
        $php    = $this->parser->parse($tokens);

        $state = new RenderState($context, $this->filters, $this->functions, $this);

        try {
            $state->execute($php);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Template error: {$e->getMessage()}", 0, $e);
        }

        if ($state->layout !== null) {
            $layoutPath = $this->resolveTemplate($state->layout);
            $layoutSource = file_get_contents($layoutPath);
            $layoutTokens = $this->lexer->tokenize($layoutSource);
            $layoutPhp = $this->parser->parse($layoutTokens);

            $layoutState = new RenderState(
                array_merge($context, ['blocks' => $state->blocks]),
                $this->filters,
                $this->functions,
                $this
            );

            try {
                $layoutState->execute($layoutPhp);
            } catch (\Throwable $e) {
                throw new \RuntimeException("Layout error: {$e->getMessage()}", 0, $e);
            }

            return $layoutState->getOutput();
        }

        return $state->getOutput();
    }

    private function resolveTemplate(string $template): string
    {
        $path = str_ends_with($template, '.php')
            ? $this->templateDir . '/' . $template
            : $this->templateDir . '/' . $template . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("Template not found: $template (tried: $path)");
        }
        return $path;
    }

    public function getCache(): FileCache
    {
        return $this->cache;
    }

    public function clearCache(): void
    {
        $this->cache->clear();
    }
}

class RenderState
{
    public ?string $layout = null;
    public array $blocks = [];
    private array $blockStack = [];
    private string $output = '';
    private array $context;
    private array $filters;
    private array $functions;
    private Engine $engine;

    public function __construct(array $context, array $filters, array $functions, Engine $engine)
    {
        $this->context  = $context;
        $this->filters  = $filters;
        $this->functions = $functions;
        $this->engine   = $engine;
    }

    public function execute(string $php): void
    {
        $state   = $this;
        $context = $this->context;
        eval($php);
    }

    public function out(mixed $value): void
    {
        if (empty($this->blockStack)) {
            $this->output .= $value;
        }
    }

    public function applyFilter(string $name, mixed $value, mixed ...$args): mixed
    {
        if (!isset($this->filters[$name])) {
            throw new \RuntimeException("Unknown filter: $name");
        }
        return ($this->filters[$name])($value, ...$args);
    }

    public function include(string $template): void
    {
        $rendered = $this->engine->render($template, $this->context);
        if (empty($this->blockStack)) {
            $this->output .= $rendered;
        } else {
            echo $rendered;
        }
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function startBlock(string $name): void
    {
        $this->blockStack[] = $name;
        ob_start();
    }

    public function endBlock(): void
    {
        $content  = ob_get_clean();
        $name     = array_pop($this->blockStack);
        $childBlocks = $this->context['blocks'] ?? [];

        if (isset($childBlocks[$name])) {
            $this->blocks[$name] = $childBlocks[$name];
        } else {
            $this->blocks[$name] = $content;
        }

        $this->output .= $this->blocks[$name];
    }

    public function callFunction(string $call): mixed
    {
        if (preg_match('/^(\w+)\((.*)\)$/', $call, $m)) {
            $name = $m[1];
            $argsStr = trim($m[2]);
            $args = $argsStr !== ''
                ? array_map(fn(string $a) => trim($a, " \t\n\r\0\x0B'\""), explode(',', $argsStr))
                : [];

            if (isset($this->functions[$name])) {
                return ($this->functions[$name])(...$args);
            }
            if (function_exists($name)) {
                return $name(...$args);
            }
            throw new \RuntimeException("Unknown function: $name");
        }
        throw new \RuntimeException("Invalid function call: $call");
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
