# Chapter 14: Projects

## Project-Based Learning for Level 0

Apply everything you've learned to build real projects. Start with the beginner project and progress to the enterprise project.

---

## Project 1: System Dashboard (Beginner)

Build a CLI dashboard that displays system information.

```php
#!/usr/bin/env php
<?php
/**
 * System Dashboard CLI
 * 
 * Shows real-time system information in your terminal
 * 
 * Usage: php system-dashboard.php [--watch]
 */

class SystemDashboard
{
    public function __construct(
        private readonly bool $watch = false
    ) {}

    public function run(): void
    {
        if ($this->watch) {
            $this->watchMode();
        } else {
            $this->singleView();
        }
    }

    private function singleView(): void
    {
        $this->render();
    }

    private function watchMode(): void
    {
        while (true) {
            system('clear');
            $this->render();
            sleep(2);
        }
    }

    private function render(): void
    {
        $this->header();
        $this->systemInfo();
        $this->cpuInfo();
        $this->memoryInfo();
        $this->diskInfo();
        $this->processInfo();
    }

    private function header(): void
    {
        echo str_repeat('═', 60) . PHP_EOL;
        echo '  PHP MASTERY COURSE — System Dashboard' . PHP_EOL;
        echo '  ' . date('Y-m-d H:i:s') . ' UTC' . PHP_EOL;
        echo str_repeat('═', 60) . PHP_EOL . PHP_EOL;
    }

    private function systemInfo(): void
    {
        echo "📦 SYSTEM" . PHP_EOL;
        echo "  OS: " . PHP_OS . PHP_EOL;
        echo "  PHP: " . PHP_VERSION . PHP_EOL;
        echo "  Hostname: " . gethostname() . PHP_EOL;
        echo "  Uptime: " . $this->getUptime() . PHP_EOL . PHP_EOL;
    }

    private function cpuInfo(): void
    {
        $cpuInfo = file_get_contents('/proc/cpuinfo');
        preg_match('/^model name\s+:\s(.+)$/m', $cpuInfo, $model);
        preg_match_all('/^processor\s+:\s(\d+)$/m', $cpuInfo, $cores);

        $load = sys_getloadavg();

        echo "🔧 CPU" . PHP_EOL;
        echo "  Model: " . ($model[1] ?? 'Unknown') . PHP_EOL;
        echo "  Cores: " . count($cores[0] ?? []) . PHP_EOL;
        echo "  Load:  " . number_format($load[0], 2) . ' ' .
                         number_format($load[1], 2) . ' ' .
                         number_format($load[2], 2) . PHP_EOL . PHP_EOL;
    }

    private function memoryInfo(): void
    {
        $memInfo = file_get_contents('/proc/meminfo');
        
        preg_match('/^MemTotal:\s+(\d+)\skB$/m', $memInfo, $total);
        preg_match('/^MemAvailable:\s+(\d+)\skB$/m', $memInfo, $avail);
        
        $totalMB = round((int)$total[1] / 1024, 1);
        $availMB = round((int)$avail[1] / 1024, 1);
        $usedMB = round($totalMB - $availMB, 1);
        $usagePercent = round(($usedMB / $totalMB) * 100, 1);

        echo "💾 MEMORY" . PHP_EOL;
        echo "  Total:     {$totalMB} MB" . PHP_EOL;
        echo "  Used:      {$usedMB} MB ({$usagePercent}%)" . PHP_EOL;
        echo "  Available: {$availMB} MB" . PHP_EOL;

        // Progress bar
        $barLen = 30;
        $filled = round($usagePercent / 100 * $barLen);
        echo "  [" . str_repeat('█', $filled) . str_repeat('░', $barLen - $filled) . "]" . PHP_EOL . PHP_EOL;
    }

    private function diskInfo(): void
    {
        echo "💿 DISK" . PHP_EOL;
        
        foreach (['/', '/home'] as $path) {
            if (!is_dir($path)) continue;
            
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;
            $percent = round(($used / $total) * 100, 1);
            
            echo "  {$path}" . PHP_EOL;
            echo "    Total: " . $this->formatBytes($total) . PHP_EOL;
            echo "    Used:  " . $this->formatBytes($used) . " ({$percent}%)" . PHP_EOL;
            echo "    Free:  " . $this->formatBytes($free) . PHP_EOL;
        }
        echo PHP_EOL;
    }

    private function processInfo(): void
    {
        echo "⚙️  PROCESSES" . PHP_EOL;
        
        // Get top 5 processes by memory
        $processes = $this->getTopProcesses(5);
        
        echo str_pad("PID", 8) . str_pad("CPU%", 8) . str_pad("MEM%", 8) . "COMMAND" . PHP_EOL;
        echo str_repeat('─', 50) . PHP_EOL;
        
        foreach ($processes as $p) {
            echo str_pad($p['pid'], 8) .
                 str_pad(number_format($p['cpu'], 1), 8) .
                 str_pad(number_format($p['mem'], 1), 8) .
                 $p['command'] . PHP_EOL;
        }
        echo PHP_EOL;
    }

    private function getTopProcesses(int $limit): array
    {
        $output = [];
        exec("ps aux --sort=-%mem | head -n " . ($limit + 1), $output);
        
        $processes = [];
        for ($i = 1; $i < count($output); $i++) {
            $parts = preg_split('/\s+/', $output[$i], 11);
            if (count($parts) < 11) continue;
            
            $processes[] = [
                'pid' => $parts[1],
                'cpu' => (float)$parts[2],
                'mem' => (float)$parts[3],
                'command' => substr($parts[10], 0, 40),
            ];
        }
        
        return $processes;
    }

    private function getUptime(): string
    {
        $uptime = (float)file_get_contents('/proc/uptime');
        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        
        return "{$days}d {$hours}h {$minutes}m";
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}

// Parse arguments
$watch = in_array('--watch', $argv ?? []);

$dashboard = new SystemDashboard($watch);
$dashboard->run();
```

---

## Project 2: Static Site Generator (Intermediate)

Build a tool that generates an HTML site from Markdown files and templates.

```php
#!/usr/bin/env php
<?php
/**
 * Static Site Generator
 * 
 * Converts Markdown files to HTML using templates
 * 
 * Usage: php ssg.php [source-dir] [output-dir]
 */

class StaticSiteGenerator
{
    public function __construct(
        private readonly string $sourceDir,
        private readonly string $outputDir,
        private readonly string $templateDir = __DIR__ . '/templates'
    ) {
        if (!is_dir($this->sourceDir)) {
            throw new RuntimeException("Source directory not found: {$this->sourceDir}");
        }
    }

    public function generate(): void
    {
        $this->cleanOutputDir();
        $this->copyAssets();
        $this->processFiles();
    }

    private function cleanOutputDir(): void
    {
        if (is_dir($this->outputDir)) {
            array_map('unlink', glob("{$this->outputDir}/*.*"));
            array_map('unlink', glob("{$this->outputDir}/**/*.*"));
        } else {
            mkdir($this->outputDir, 0755, true);
        }
    }

    private function copyAssets(): void
    {
        $assetsDir = "{$this->sourceDir}/assets";
        if (is_dir($assetsDir)) {
            $this->copyRecursive($assetsDir, "{$this->outputDir}/assets");
        }
    }

    private function processFiles(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->sourceDir, 
                RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $this->processFile($file);
            }
        }
    }

    private function processFile(SplFileInfo $file): void
    {
        $content = file_get_contents($file->getPathname());
        $html = $this->markdownToHtml($content);
        
        $relativePath = str_replace($this->sourceDir . '/', '', $file->getPath());
        $outputPath = "{$this->outputDir}/{$relativePath}";
        
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        $outputFile = "{$outputPath}/" . $file->getBasename('.md') . '.html';
        $rendered = $this->applyTemplate($html, [
            'title' => $this->extractTitle($content),
            'content' => $html,
        ]);

        file_put_contents($outputFile, $rendered);
        echo "Generated: {$outputFile}" . PHP_EOL;
    }

    private function markdownToHtml(string $markdown): string
    {
        // Simple Markdown parser
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        
        // Bold and italic
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        
        // Code blocks
        $html = preg_replace('/```(\w*)\n(.*?)```/s', '<pre><code>$2</code></pre>', $html);
        $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);
        
        // Links
        $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $html);
        
        // Lists
        $html = preg_replace('/^\- (.+)$/m', '<li>$1</li>', $html);
        
        // Paragraphs
        $lines = explode("\n\n", $html);
        $html = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && !str_starts_with($line, '<')) {
                $html .= "<p>{$line}</p>\n";
            } else {
                $html .= $line . "\n";
            }
        }
        
        return $html;
    }

    private function applyTemplate(string $content, array $data): string
    {
        $template = $this->getTemplate('page');
        $template = str_replace(
            ['{{title}}', '{{content}}', '{{year}}'],
            [htmlspecialchars($data['title']), $content, date('Y')],
            $template
        );
        return $template;
    }

    private function getTemplate(string $name): string
    {
        $path = "{$this->templateDir}/{$name}.html";
        if (!file_exists($path)) {
            // Default template
            return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{{title}} - My Site</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header>
        <h1>My Site</h1>
    </header>
    <main>
        <h1>{{title}}</h1>
        {{content}}
    </main>
    <footer>
        <p>&copy; {{year}} My Site</p>
    </footer>
</body>
</html>
HTML;
        }
        return file_get_contents($path);
    }

    private function extractTitle(string $markdown): string
    {
        if (preg_match('/^# (.+)$/m', $markdown, $matches)) {
            return $matches[1];
        }
        return 'Untitled';
    }

    private function copyRecursive(string $src, string $dst): void
    {
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        
        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;
            
            $srcFile = "{$src}/{$file}";
            $dstFile = "{$dst}/{$file}";
            
            if (is_dir($srcFile)) {
                $this->copyRecursive($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
        closedir($dir);
    }
}

// CLI usage
$sourceDir = $argv[1] ?? __DIR__ . '/content';
$outputDir = $argv[2] ?? __DIR__ . '/dist';

try {
    $generator = new StaticSiteGenerator($sourceDir, $outputDir);
    $generator->generate();
    echo "Site generated successfully in {$outputDir}" . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
```

---

## Project 3: PHP MVC Framework (Professional)

Build a lightweight MVC framework demonstrating all architectural concepts.

```php
<?php
/**
 * MiniPHP Framework - A lightweight MVC framework
 * 
 * File: public/index.php (Entry Point)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MiniPHP\Kernel;
use MiniPHP\Request;
use MiniPHP\Response;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create kernel
$kernel = new Kernel();

// Handle request
$request = Request::fromGlobals();
$response = $kernel->handle($request);
$response->send();
```

```php
<?php
/**
 * MiniPHP Framework - Kernel
 */

namespace MiniPHP;

class Kernel
{
    private Router $router;
    private array $middleware = [];
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();
        $this->registerServices();
        $this->registerRoutes();
    }

    public function handle(Request $request): Response
    {
        try {
            // Global middleware
            $this->runMiddleware($request, 'global');
            
            // Route matching
            $route = $this->router->match($request);
            
            // Route middleware
            $this->runMiddleware($request, $route->getMiddleware());
            
            // Call controller
            $response = $this->callController($route, $request);
            
            return $response;
            
        } catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        } catch (\Throwable $e) {
            if (getenv('APP_DEBUG')) {
                return new Response((string)$e, 500);
            }
            return new Response('Internal Server Error', 500);
        }
    }

    private function registerServices(): void
    {
        $this->container->set(PDO::class, function () {
            return new PDO(
                getenv('DB_DSN'),
                getenv('DB_USER'),
                getenv('DB_PASS'),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        });
        
        $this->container->set(View::class, function () {
            return new View(__DIR__ . '/../templates');
        });
    }

    private function registerRoutes(): void
    {
        require_once __DIR__ . '/../config/routes.php';
    }

    private function runMiddleware(Request $request, array $middleware): void
    {
        foreach ($middleware as $mw) {
            $instance = $this->container->get($mw);
            $instance->handle($request);
        }
    }

    private function callController(Route $route, Request $request): Response
    {
        $controller = $this->container->get($route->getController());
        $action = $route->getAction();
        $params = $route->getParams();
        
        // Inject request and params
        return $controller->$action($request, ...$params);
    }
}
```

```php
<?php
/**
 * MiniPHP Framework - Router
 */

namespace MiniPHP;

class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];

    public function group(array $attributes, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $attributes['middleware'] ?? []);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function match(Request $request): Route
    {
        $method = $request->getMethod();
        $uri = $request->getPath();
        
        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $route;
            }
        }
        
        throw new HttpException('Not Found', 404);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = new Route(
            $method,
            $path,
            $handler[0],
            $handler[1],
            array_merge($this->groupMiddleware, $middleware)
        );
    }
}
```

```php
<?php
/**
 * MiniPHP Framework - Route
 */

namespace MiniPHP;

class Route
{
    private array $params = [];

    public function __construct(
        private readonly string $method,
        private readonly string $pattern,
        private readonly string $controller,
        private readonly string $action,
        private readonly array $middleware = []
    ) {}

    public function matches(string $method, string $uri): bool
    {
        if ($this->method !== $method) {
            return false;
        }
        
        $regex = $this->patternToRegex($this->pattern);
        
        if (preg_match($regex, $uri, $matches)) {
            $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }
        
        return false;
    }

    public function getController(): string { return $this->controller; }
    public function getAction(): string { return $this->action; }
    public function getParams(): array { return $this->params; }
    public function getMiddleware(): array { return $this->middleware; }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }
}
```

---

## Project 4: Multi-Tenant SaaS Platform (Enterprise)

Build the foundation of a multi-tenant SaaS platform demonstrating enterprise-level architecture.

*(Due to scope, this project spans ~2000+ lines. Key components shown:)*

```php
<?php
/**
 * Multi-Tenant Architecture - Tenant Context
 */

namespace SaaS\Core;

class TenantContext
{
    private static ?Tenant $current = null;

    public static function set(Tenant $tenant): void
    {
        self::$current = $tenant;
    }

    public static function current(): Tenant
    {
        if (self::$current === null) {
            throw new \RuntimeException('No tenant context set');
        }
        return self::$current;
    }

    public static function id(): int
    {
        return self::current()->id;
    }
}
```

```php
<?php
/**
 * Multi-Tenant Architecture - Database Manager
 */

namespace SaaS\Core\Database;

class TenantDatabaseManager
{
    private array $connections = [];

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password
    ) {}

    public function forTenant(Tenant $tenant): \PDO
    {
        $key = $tenant->database_name;
        
        if (!isset($this->connections[$key])) {
            $this->connections[$key] = new \PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$tenant->database_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
        }
        
        return $this->connections[$key];
    }
}
```

```php
<?php
/**
 * Multi-Tenant Architecture - Tenant Middleware
 */

namespace SaaS\Http\Middleware;

use SaaS\Core\TenantContext;
use SaaS\Core\Tenant;
use SaaS\Core\Database\TenantDatabaseManager;
use MiniPHP\Request;

class TenantMiddleware
{
    public function __construct(
        private readonly TenantDatabaseManager $dbManager
    ) {}

    public function handle(Request $request): void
    {
        // Extract tenant from subdomain or header
        $tenantId = $this->resolveTenant($request);
        
        // Load tenant
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant || !$tenant->isActive()) {
            throw new \RuntimeException('Invalid or inactive tenant', 403);
        }
        
        // Set context
        TenantContext::set($tenant);
        
        // Switch database connection
        $pdo = $this->dbManager->forTenant($tenant);
        Database::setConnection($pdo);
    }

    private function resolveTenant(Request $request): int
    {
        // From subdomain: tenant1.app.example.com
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = $parts[0];
        
        // From header (API)
        $header = $request->header('X-Tenant-ID');
        
        return (int)($header ?? $subdomain);
    }
}
```

---

## Project Structure Summary

```
level-0-projects/
├── project-01-portfolio/
│   ├── index.html
│   ├── css/
│   │   └── style.css
│   └── README.md
├── project-02-system-dashboard/
│   ├── dashboard.php      # PHP CLI version
│   ├── dashboard.sh       # Bash version
│   ├── dashboard.html     # Generated output
│   └── README.md
├── project-03-http-inspector/
│   ├── inspector.php
│   └── README.md
├── project-04-file-organizer/
│   ├── organizer.php
│   └── README.md
└── project-05-markdown-converter/
    ├── converter.php
    ├── sample.md
    └── README.md
```

---

## Evaluation Criteria

| Level | Criteria | Points |
|-------|----------|--------|
| Beginner | Code works correctly | 30 |
| | Proper error handling | 20 |
| | Code organization | 20 |
| | Documentation | 15 |
| | Output format | 15 |
| **Total** | | **100** |
| Intermediate | All beginner criteria | 20 |
| | Architecture (MVC, patterns) | 25 |
| | Security practices | 20 |
| | Performance considerations | 20 |
| | Testing | 15 |
| **Total** | | **100** |
| Professional | All intermediate criteria | 15 |
| | Dependency injection | 15 |
| | Service container | 15 |
| | Middleware pipeline | 15 |
| | Configuration management | 10 |
| | Error handling | 10 |
| | Extensibility | 10 |
| | Documentation | 10 |
| **Total** | | **100** |
| Enterprise | All professional criteria | 10 |
| | Multi-tenancy | 20 |
| | Scalability patterns | 20 |
| | Monitoring/observability | 15 |
| | CI/CD configuration | 15 |
| | Security hardening | 10 |
| | Performance optimization | 10 |
| **Total** | | **100** |

---

*End of Chapter 14. Proceed to Chapter 15: Career Roadmaps.*
