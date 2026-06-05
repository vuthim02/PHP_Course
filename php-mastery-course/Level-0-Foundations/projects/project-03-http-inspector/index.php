<?php
/**
 * index.php — HTTP Request Inspector
 *
 * Displays every detail of an incoming HTTP request:
 *   - Request method & URI
 *   - Headers (via getallheaders())
 *   - GET parameters
 *   - POST parameters
 *   - Raw request body
 *   - Cookies
 *   - Server environment variables ($_SERVER)
 *
 * Visit in browser:
 *   php -S localhost:8080
 *   → http://localhost:8080
 *
 * Pass query params to inspect GET:
 *   → http://localhost:8080/?name=Alex&role=developer
 *
 * Submit form-demo.html to inspect POST method, headers, and body.
 *
 * PHP 8.x features:
 *   - match expression, named arguments via compact array
 *   - str_contains, str_starts_with
 *   - Nullsafe operator (php 8.0+)
 *   - readonly promotion mindset
 */

declare(strict_types=1);

// ─── Helper: Pretty-print a key/value array ──────────────────────

function renderTable(string $title, array $data, string $empty = '—'): string
{
    if (empty($data)) {
        return sprintf(
            "<div class=\"section\"><h2>%s</h2><p class=\"empty\">%s</p></div>\n",
            htmlspecialchars($title, ENT_QUOTES),
            $empty
        );
    }

    $rows = '';
    foreach ($data as $key => $value) {
        $k = htmlspecialchars((string) $key, ENT_QUOTES);
        $v = htmlspecialchars(
            is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES) : (string) $value,
            ENT_QUOTES
        );
        $rows .= "<tr><td class=\"key\">{$k}</td><td class=\"val\">{$v}</td></tr>\n";
    }

    return sprintf(
        "<div class=\"section\"><h2>%s</h2><table>%s</table></div>\n",
        htmlspecialchars($title, ENT_QUOTES),
        $rows
    );
}

// ─── Gather Data ─────────────────────────────────────────────────

$method   = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$uri      = $_SERVER['REQUEST_URI'] ?? '/';
$protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';

// Request headers
$headers = function_exists('getallheaders') ? getallheaders() : [];
// Normalise header case (getallheaders() may return mixed case)
$headers = array_change_key_case($headers, CASE_LOWER);

// GET params
$getParams = $_GET;

// POST params
$postParams = $_POST;

// Raw body (for PUT/PATCH/DELETE or JSON payloads)
$rawBody = file_get_contents('php://input');

// Cookies
$cookies = $_COOKIE;

// Selected $_SERVER entries relevant to the request
$serverKeys = [
    'REQUEST_METHOD', 'REQUEST_URI', 'SERVER_PROTOCOL',
    'HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT',
    'REMOTE_ADDR', 'REMOTE_PORT',
    'QUERY_STRING', 'CONTENT_TYPE', 'CONTENT_LENGTH',
    'SCRIPT_FILENAME', 'DOCUMENT_ROOT',
    'REQUEST_TIME_FLOAT',
];
$serverVars = [];
foreach ($serverKeys as $key) {
    if (isset($_SERVER[$key])) {
        $serverVars[$key] = $_SERVER[$key];
    }
}

if (isset($serverVars['REQUEST_TIME_FLOAT'])) {
    $serverVars['REQUEST_TIME_FLOAT'] = sprintf(
        '%.4f (%s)',
        (float) $serverVars['REQUEST_TIME_FLOAT'],
        date('H:i:s.v', (int) $serverVars['REQUEST_TIME_FLOAT'])
    );
}

// ─── Render Page ─────────────────────────────────────────────────

$htmlMethod  = htmlspecialchars($method, ENT_QUOTES);
$htmlUri     = htmlspecialchars($uri, ENT_QUOTES);
$htmlProto   = htmlspecialchars($protocol, ENT_QUOTES);

$headerBlock   = renderTable('Request Headers', $headers);
$getBlock      = renderTable('GET Parameters', $getParams);
$postBlock     = renderTable('POST Parameters', $postParams);
$cookieBlock   = renderTable('Cookies', $cookies);
$serverBlock   = renderTable('Server Variables', $serverVars);

$bodyBlock = '';
if ($rawBody !== false && $rawBody !== '') {
    $escaped = htmlspecialchars($rawBody, ENT_QUOTES);
    $bodyBlock = <<<BODY
    <div class="section">
      <h2>Raw Request Body</h2>
      <pre class="raw-body">{$escaped}</pre>
    </div>
    BODY;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HTTP Inspector — Request Details</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0b1120; color: #e2e8f0; padding: 2rem; }
  .container { max-width: 960px; margin: 0 auto; }
  h1 { font-size: 1.8rem; color: #38bdf8; margin-bottom: 0.25rem; }
  .request-line {
    font-family: 'Fira Code', 'Cascadia Code', monospace;
    background: #1e293b; padding: 1rem 1.25rem; border-radius: 8px;
    margin: 1rem 0 2rem; border-left: 4px solid #38bdf8;
    font-size: 1.1rem;
  }
  .method { color: #fbbf24; font-weight: 700; }
  .uri    { color: #a5b4fc; }
  .proto  { color: #34d399; }
  .section { margin-bottom: 1.75rem; }
  .section h2 {
    font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em;
    color: #64748b; margin-bottom: 0.75rem;
  }
  table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
  td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #1e293b; vertical-align: top; }
  .key { color: #94a3b8; font-family: 'Fira Code', monospace; width: 240px; white-space: nowrap; }
  .val { color: #f1f5f9; word-break: break-all; }
  tr:hover td { background: #1a2639; }
  .empty { color: #475569; font-style: italic; }
  .raw-body {
    background: #1e293b; padding: 1rem; border-radius: 8px;
    font-family: 'Fira Code', monospace; font-size: 0.85rem;
    overflow-x: auto; white-space: pre-wrap; color: #a5b4fc;
  }
  .nav-links { margin: 1.5rem 0; display: flex; gap: 1rem; }
  .nav-links a { color: #38bdf8; text-decoration: none; font-size: 0.9rem; }
  .nav-links a:hover { text-decoration: underline; }
  footer { text-align: center; color: #475569; margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #1e293b; font-size: 0.8rem; }
</style>
</head>
<body>
<div class="container">
  <h1>HTTP Request Inspector</h1>
  <div class="nav-links">
    <a href="form-demo.html">&larr; Open Form Demo</a>
    <a href="?foo=bar&baz=qux">Test GET: ?foo=bar&baz=qux</a>
    <a href="?name=Alex&role=developer">Test GET: ?name=Alex</a>
  </div>

  <div class="request-line">
    <span class="method"><?= $htmlMethod ?></span>
    <span class="uri"><?= $htmlUri ?></span>
    <span class="proto"><?= $htmlProto ?></span>
  </div>

  <?= $headerBlock ?>
  <?= $getBlock ?>
  <?= $postBlock ?>
  <?= $bodyBlock ?>
  <?= $cookieBlock ?>
  <?= $serverBlock ?>
</div>

<footer>HTTP Inspector — Level 0 Foundations Project</footer>
</body>
</html>
