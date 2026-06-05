<?php

declare(strict_types=1);

namespace DebuggerProfiler\Renderer;

use DebuggerProfiler\Dump;

class HtmlRenderer
{
    public function renderError(\Throwable $e): string
    {
        $dump     = new Dump();
        $sections = [
            $this->header($e),
            $this->section('Stack Trace', $this->renderStackTrace($e)),
            $this->section('Request Data', $this->renderRequestData()),
            $this->section('Server Data', $this->renderServerData()),
            $this->section('Loaded Classes', $this->renderLoadedClasses()),
        ];

        return $this->layout(implode("\n", $sections), (string) $e->getCode());
    }

    public function renderProfiler(array $report): string
    {
        $rows = '';
        foreach ($report as $key => $val) {
            $v = is_array($val) ? '<pre>' . Dump::dump($val) . '</pre>' : Dump::dump($val);
            $rows .= "<tr><td>" . Dump::dump($key) . "</td><td>$v</td></tr>\n";
        }

        return $this->layout("
            <h1>Profiler Report</h1>
            <table border='1' cellpadding='8' cellspacing='0' style='width:100%;border-collapse:collapse'>
                <thead><tr><th>Key</th><th>Value</th></tr></thead>
                <tbody>$rows</tbody>
            </table>
        ", 'profiler');
    }

    private function header(\Throwable $e): string
    {
        $class  = (new \ReflectionClass($e))->getShortName();
        $msg    = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $file   = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $line   = $e->getLine();
        $code   = $e->getCode();

        return "
            <div style='background:#f8d7da;padding:20px;border-radius:8px;margin-bottom:20px'>
                <h1 style='color:#721c24;margin:0 0 10px 0'>$class: $msg</h1>
                <p style='color:#856404;margin:0'>
                    <strong>File:</strong> $file<br>
                    <strong>Line:</strong> $line<br>
                    <strong>Code:</strong> $code
                </p>
            </div>
        ";
    }

    private function renderStackTrace(\Throwable $e): string
    {
        $trace = $e->getTrace();
        $html  = "<ol style='font-family:monospace;font-size:13px'>\n";

        foreach ($trace as $i => $frame) {
            $file = $frame['file'] ?? '[internal]';
            $line = $frame['line'] ?? '?';
            $fn   = $frame['function'] ?? 'unknown';
            $class = $frame['class'] ?? '';
            $type  = $frame['type'] ?? '';

            $html .= "<li style='margin-bottom:8px'>";
            $html .= "<strong>#$i</strong> ";
            $html .= htmlspecialchars("$file($line)", ENT_QUOTES, 'UTF-8');
            $html .= "<br><span style='color:#666'>";
            $html .= htmlspecialchars("$class$type$fn()", ENT_QUOTES, 'UTF-8');
            $html .= "</span></li>\n";
        }

        $html .= "</ol>";
        return $html;
    }

    private function renderRequestData(): string
    {
        $data = [
            'GET'     => $_GET,
            'POST'    => $_POST,
            'COOKIE'  => $_COOKIE,
            'FILES'   => $_FILES,
            'HEADERS' => function_exists('getallheaders') ? getallheaders() : [],
        ];

        return '<pre>' . Dump::dump($data) . '</pre>';
    }

    private function renderServerData(): string
    {
        $keys = ['REQUEST_METHOD', 'REQUEST_URI', 'HTTP_HOST', 'SCRIPT_FILENAME', 'QUERY_STRING', 'SERVER_PROTOCOL'];
        $data = [];
        foreach ($keys as $k) {
            $data[$k] = $_SERVER[$k] ?? 'N/A';
        }
        return '<pre>' . Dump::dump($data) . '</pre>';
    }

    private function renderLoadedClasses(): string
    {
        $classes = get_declared_classes();
        $filtered = array_values(array_filter($classes, fn(string $c) => !str_starts_with($c, 'PHP') && !str_starts_with($c, 'Zend') && !str_starts_with($c, 'Composer')));
        return '<pre>' . Dump::dump(array_slice($filtered, 0, 50)) . ' ...</pre>';
    }

    private function section(string $title, string $content): string
    {
        return "
            <div style='background:#fff;border:1px solid #ddd;border-radius:8px;padding:15px;margin-bottom:15px'>
                <h2 style='margin:0 0 10px 0;color:#333;border-bottom:2px solid #007bff;padding-bottom:5px'>$title</h2>
                $content
            </div>
        ";
    }

    private function layout(string $body, string $type = ''): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>PHP Debugger :: $type</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; margin: 20px; padding: 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 13px; line-height: 1.5; }
        td { vertical-align: top; }
        table { word-break: break-word; }
    </style>
</head>
<body>
    <div style='max-width:1200px;margin:0 auto'>
        $body
    </div>
</body>
</html>
HTML;
    }
}
