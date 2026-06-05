# Template Engine with Caching

A lightweight PHP template engine inspired by Twig/Blade with variable output, control structures, template inheritance, caching, custom filters, and a streaming tokenizer.

## Advanced PHP Concepts Demonstrated

| Concept | File |
|---|---|
| **Regular Expressions** | `src/Lexer.php:17-22` — `preg_split` and `preg_match` for tokenizing template syntax |
| **Output Buffering** | `src/Engine.php` (RenderState) — `ob_start()`/`ob_get_clean()` for block capture and template inheritance |
| **Streams / File I/O** | `src/Cache/FileCache.php` — `file_get_contents`, `file_put_contents` with locking for cache |
| **Generators (via iteration)** | `src/Parser.php:54-64` — `array_map` / `foreach` chains over token arrays |
| **Performance Optimization** | `src/Engine.php:49-62` — Compiled template caching with filemtime invalidation |
| **Anonymous functions** | `src/Extension/EscaperExtension.php` — Filter and function registrations as closures |
| **Late Static Binding (variation)** | `Engine::render()` with `RenderState` separation of concerns |
| **`var_export` for code gen** | `src/Parser.php:136-151` — Generates PHP code strings for compiled templates |

## Features

- **Variable Output** — `{{ var }}` with auto-escaping and nested access (`{{ user.name }}`)
- **Filter Chains** — `{{ name | upper | e }}`, custom filter registration
- **Control Structures** — `{% if %}`, `{% for %}`, `{% for key, val in iterable %}`
- **Template Inheritance** — `{% extends "base" %}` with `{% block name %}` override
- **Partials** — `{% include "partial" %}` for reusable components
- **Template Caching** — Compiled templates cached to filesystem, auto-invalidated on file change
- **Custom Functions** — `{{ now('Y') }}`, `{{ dump(var) }}`, register your own
- **Set Variables** — `{% set var = value %}` in templates
- **Direct string rendering** — `renderString()` for inline templates

## Setup

```bash
composer install
```

## Run Demo

```bash
php examples/template-demo.php
```

## Usage

```php
<?php

use TemplateEngine\Engine;

$engine = new Engine('/path/to/templates');

// Render a template with context
$html = $engine->render('page', [
    'name'  => 'World',
    'users' => [['name' => 'Alice', 'email' => 'alice@example.com']],
    'items' => ['PHP' => 'Language'],
]);

// Template file (page.php):
// {% extends "base" %}
// {% block content %}
//   <h1>Hello {{ name }}</h1>
//   {% for user in users %}
//     <p>{{ user.name }}</p>
//   {% endfor %}
// {% endblock %}

// Render a string directly
$html = $engine->renderString('<h1>{{ title | upper }}</h1>', ['title' => 'hello']);
```

## What Makes It "Advanced PHP"

This template engine demonstrates **regular expression** tokenization (non-trivial `preg_split` with delimiters), **output buffering** for block capture and template inheritance, **filesystem caching** with invalidation, and **code generation** using `var_export`. The compiled templates are plain PHP stored on disk — the same strategy used by Twig and Blade. The `RenderState` class encapsulates rendering context using a **delegation pattern**, and filters/functions are registered as **closures** from extensions.
