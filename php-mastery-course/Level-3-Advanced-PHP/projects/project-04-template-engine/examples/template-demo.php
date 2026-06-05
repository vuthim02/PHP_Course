<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TemplateEngine\Engine;

echo "=== PHP Template Engine Demo ===\n\n";

$engine = new Engine(__DIR__ . '/../templates');

$users = [
    ['name' => 'Alice Johnson', 'email' => 'alice@example.com'],
    ['name' => 'Bob Smith',     'email' => 'bob@example.com'],
    ['name' => 'Charlie Brown', 'email' => 'charlie@example.com'],
];

$items = [
    'PHP'     => 'Hypertext Preprocessor',
    'HTML'    => 'HyperText Markup Language',
    'CSS'     => 'Cascading Style Sheets',
    'SQL'     => 'Structured Query Language',
];

$context = [
    'name'         => 'World',
    'users'        => $users,
    'items'        => $items,
    'show_details' => true,
];

echo "Rendering template with extends, blocks, loops, conditionals...\n\n";

$start = microtime(true);
$output = $engine->render('page', $context);
$elapsed = (microtime(true) - $start) * 1000;

echo $output;

echo "\n\n--- Stats ---\n";
printf("Render time: %.2f ms\n", $elapsed);
printf("Cache dir: %s\n", $engine->getCache()->getCacheDirectory());

echo "\n\n--- Render with caching (second call) ---\n";
$start2 = microtime(true);
$output2 = $engine->render('page', $context);
$elapsed2 = (microtime(true) - $start2) * 1000;
printf("Cached render: %.2f ms\n", $elapsed2);

echo "\n\n--- String rendering (renderString) ---\n";
$stringSource = '<h3>Hello {{ name | upper }}</h3><p>Direct string rendering.</p>';
echo $engine->renderString($stringSource, ['name' => 'string template']);

echo "\n\n--- Filters Demo ---\n";
$filterSource = <<<'TMPL'
<ul>
    <li>upper: {{ name | upper }}</li>
    <li>lower: {{ name | lower }}</li>
    <li>length: {{ name | length }}</li>
    <li>default: {{ undefined_var | default('fallback') }}</li>
    <li>json: {{ data | json }}</li>
</ul>
TMPL;
echo $engine->renderString($filterSource, ['name' => 'PhP', 'data' => ['foo', 'bar', 'baz']]);

echo "\nDemo completed successfully!\n";
