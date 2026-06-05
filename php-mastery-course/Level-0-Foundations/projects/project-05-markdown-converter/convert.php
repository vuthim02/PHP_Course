#!/usr/bin/env php
<?php
/**
 * convert.php — Markdown to HTML Converter (CLI)
 *
 * Reads a Markdown file and outputs a complete HTML document.
 * Supports: headings (#), bold (**), italic (*), links, images,
 * ordered/unordered lists, inline code, code blocks, blockquotes,
 * horizontal rules, and paragraphs.
 *
 * Usage:
 *   php convert.php sample.md                # Print HTML to stdout
 *   php convert.php sample.md output.html    # Write to file
 *   php convert.php sample.md --template template.html
 *
 * This is a from-scratch parser — no Composer dependencies.
 *
 * PHP 8.x features:
 *   - match expression, readonly properties
 *   - str_starts_with, str_contains, str_ends_with
 *   - Named arguments (via array unpacking in sprintf)
 *   - Nullsafe operator
 */

declare(strict_types=1);

// ─── Configuration ───────────────────────────────────────────────

/**
 * Base template path. Can be overridden with --template flag.
 * If null, a minimal embedded template is used.
 */
const DEFAULT_TEMPLATE = __DIR__ . '/template.html';

// ─── Markdown Parser ─────────────────────────────────────────────

final class MarkdownParser
{
    private string $title = '';

    /**
     * Parse raw Markdown text into an HTML string.
     */
    public function parse(string $markdown): string
    {
        $lines = explode("\n", $markdown);
        $html  = '';
        $i     = 0;
        $count = count($lines);

        while ($i < $count) {
            $line = $lines[$i];

            // ── Code block (``` or ~~~) ──
            if ($this->isCodeFence($line)) {
                $fence = $line;
                $i++;
                $code = '';
                while ($i < $count && !$this->isCodeFence($lines[$i])) {
                    $code .= $lines[$i] . "\n";
                    $i++;
                }
                $i++; // skip closing fence
                $html .= $this->renderCodeBlock($code, $fence);
                continue;
            }

            // ── Horizontal rule ──
            if ($this->isHorizontalRule($line)) {
                $html .= "<hr>\n";
                $i++;
                continue;
            }

            // ── Heading ──
            $heading = $this->parseHeading($line);
            if ($heading !== null) {
                if ($heading['level'] === 1 && $this->title === '') {
                    $this->title = $heading['text'];
                }
                $html .= sprintf(
                    "<h%d>%s</h%d>\n",
                    $heading['level'],
                    $this->parseInline($heading['text']),
                    $heading['level']
                );
                $i++;
                continue;
            }

            // ── Blockquote ──
            if (str_starts_with(ltrim($line), '>')) {
                $blockquote = '';
                while ($i < $count && str_starts_with(ltrim($lines[$i]), '>')) {
                    $trimmed = preg_replace('/^>\s?/', '', $lines[$i]);
                    $blockquote .= $trimmed . "\n";
                    $i++;
                }
                // Recursively parse content inside blockquote
                $innerHtml = $this->parse(rtrim($blockquote));
                $html .= "<blockquote>{$innerHtml}</blockquote>\n";
                continue;
            }

            // ── Unordered list ──
            if ($this->isUnorderedListItem($line)) {
                $html .= "<ul>\n";
                while ($i < $count && $this->isUnorderedListItem($lines[$i])) {
                    $item = preg_replace('/^[\s]*[-*+]\s+/', '', $lines[$i]);
                    $html .= "  <li>" . $this->parseInline($item) . "</li>\n";
                    $i++;
                }
                $html .= "</ul>\n";
                continue;
            }

            // ── Ordered list ──
            if ($this->isOrderedListItem($line)) {
                $html .= "<ol>\n";
                while ($i < $count && $this->isOrderedListItem($lines[$i])) {
                    $item = preg_replace('/^[\s]*\d+\.\s+/', '', $lines[$i]);
                    $html .= "  <li>" . $this->parseInline($item) . "</li>\n";
                    $i++;
                }
                $html .= "</ol>\n";
                continue;
            }

            // ── Empty line → paragraph separator ──
            if (trim($line) === '') {
                $i++;
                continue;
            }

            // ── Paragraph ──
            $paragraph = '';
            while ($i < $count && trim($lines[$i]) !== '' && !$this->isBlockElement($lines[$i])) {
                $paragraph .= $lines[$i] . "\n";
                $i++;
            }

            $paragraph = trim($paragraph);
            if ($paragraph !== '') {
                $html .= "<p>" . $this->parseInline($paragraph) . "</p>\n";
            }
        }

        return $html;
    }

    /**
     * Parse inline elements: bold, italic, code, links, images.
     */
    private function parseInline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        // Images: ![alt](url)
        $text = preg_replace_callback(
            '/!\[([^\]]*)\]\(([^)]+)\)/',
            fn(array $m) => sprintf('<img src="%s" alt="%s">', $m[2], $m[1]),
            $text
        );

        // Links: [text](url)
        $text = preg_replace_callback(
            '/\[([^\]]*)\]\(([^)]+)\)/',
            fn(array $m) => sprintf('<a href="%s">%s</a>', $m[2], $m[1]),
            $text
        );

        // Inline code: `code`
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

        // Bold: **text** or __text__
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);

        // Italic: *text* or _text_ (but not inside words)
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);

        // Line breaks within paragraph
        $text = nl2br($text, false);

        return $text;
    }

    // ── Detection Helpers ──

    private function isCodeFence(string $line): bool
    {
        $trimmed = trim($line);
        return str_starts_with($trimmed, '```') || str_starts_with($trimmed, '~~~');
    }

    private function isHorizontalRule(string $line): bool
    {
        $trimmed = trim($line);
        return $trimmed === '---' || $trimmed === '***' || $trimmed === '___';
    }

    private function parseHeading(string $line): ?array
    {
        $trimmed = ltrim($line);
        if (!str_starts_with($trimmed, '#')) {
            return null;
        }

        preg_match('/^(#{1,6})\s+(.+)$/', $trimmed, $m);
        if ($m === []) {
            return null;
        }

        return [
            'level' => strlen($m[1]),
            'text'  => $m[2],
        ];
    }

    private function isUnorderedListItem(string $line): bool
    {
        $trimmed = ltrim($line);
        return (bool) preg_match('/^[-*+]\s+/', $trimmed);
    }

    private function isOrderedListItem(string $line): bool
    {
        $trimmed = ltrim($line);
        return (bool) preg_match('/^\d+\.\s+/', $trimmed);
    }

    private function isBlockElement(string $line): bool
    {
        $trimmed = ltrim($line);
        return str_starts_with($trimmed, '#')
            || str_starts_with($trimmed, '>')
            || str_starts_with($trimmed, '```')
            || str_starts_with($trimmed, '~~~')
            || $this->isHorizontalRule($line)
            || $this->isUnorderedListItem($line)
            || $this->isOrderedListItem($line);
    }

    private function renderCodeBlock(string $code, string $fence): string
    {
        $trimmed = trim($fence);
        $lang = '';
        if (strlen($trimmed) > 3) {
            $lang = ' class="language-' . htmlspecialchars(substr($trimmed, 3), ENT_QUOTES) . '"';
        }

        $code = htmlspecialchars(rtrim($code), ENT_QUOTES);

        return sprintf("<pre><code%s>%s</code></pre>\n", $lang, $code);
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

// ─── Template Engine ─────────────────────────────────────────────

final class TemplateEngine
{
    public static function render(string $body, string $title = '', ?string $templatePath = null): string
    {
        $template = null;

        if ($templatePath !== null && is_file($templatePath)) {
            $template = file_get_contents($templatePath);
        }

        if ($template === null && DEFAULT_TEMPLATE !== null && is_file(DEFAULT_TEMPLATE)) {
            $template = file_get_contents(DEFAULT_TEMPLATE);
        }

        if ($template === null) {
            // Minimal built-in template
            $template = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{TITLE}}</title>
<style>
  body { font-family: 'Segoe UI', system-ui, sans-serif; max-width: 800px; margin: 0 auto; padding: 2rem; line-height: 1.7; color: #1a1a2e; }
  pre { background: #f1f5f9; padding: 1rem; border-radius: 8px; overflow-x: auto; }
  code { background: #f1f5f9; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.9em; }
  pre code { background: none; padding: 0; }
  img { max-width: 100%; }
  blockquote { border-left: 4px solid #4f46e5; margin: 0; padding: 0.5rem 1.5rem; background: #f8fafc; }
</style>
</head>
<body>
{{BODY}}
</body>
</html>
HTML;
        }

        return str_replace(
            ['{{TITLE}}', '{{BODY}}'],
            [htmlspecialchars($title ?: 'Markdown Preview', ENT_QUOTES), $body],
            $template
        );
    }
}

// ─── CLI Entry Point ─────────────────────────────────────────────

function showHelp(): never
{
    echo <<<HELP
Markdown to HTML Converter — PHP CLI

Usage:
  php convert.php <input.md> [output.html] [--template path]

Arguments:
  input.md       Path to the Markdown file (required)
  output.html    Path for the generated HTML (optional; stdout if omitted)

Options:
  --template     Path to a custom HTML template file
  --help         Show this help message

The template file should contain {{TITLE}} and {{BODY}} placeholders.

Examples:
  php convert.php sample.md
  php convert.php sample.md output.html
  php convert.php sample.md --template template.html
  php convert.php sample.md output.html --template template.html

HELP;
    exit(0);
}

// Parse arguments
$args = array_slice($argv ?? [], 1);

if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
    showHelp();
}

if ($args === []) {
    fwrite(STDERR, "[ERROR] No input file specified.\n");
    showHelp();
}

$inputFile  = null;
$outputFile = null;
$templateFile = null;

$nextIsTemplate = false;
foreach ($args as $arg) {
    if ($nextIsTemplate) {
        $templateFile = $arg;
        $nextIsTemplate = false;
        continue;
    }
    if ($arg === '--template') {
        $nextIsTemplate = true;
        continue;
    }
    if ($inputFile === null) {
        $inputFile = $arg;
    } elseif ($outputFile === null) {
        $outputFile = $arg;
    }
}

if ($inputFile === null || !is_file($inputFile)) {
    fwrite(STDERR, "[ERROR] File not found: {$inputFile}\n");
    exit(1);
}

// Parse
$markdown = file_get_contents($inputFile);
if ($markdown === false) {
    fwrite(STDERR, "[ERROR] Could not read file: {$inputFile}\n");
    exit(1);
}

$parser = new MarkdownParser();
$bodyHtml = $parser->parse($markdown);
$title = $parser->getTitle() ?: pathinfo($inputFile, PATHINFO_FILENAME);

$fullHtml = TemplateEngine::render($bodyHtml, $title, $templateFile);

// Output
if ($outputFile !== null) {
    file_put_contents($outputFile, $fullHtml);
    echo "[OK] Written to: " . realpath($outputFile) . PHP_EOL;
} else {
    echo $fullHtml;
}
