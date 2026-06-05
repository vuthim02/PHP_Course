# PHP Standards Recommendations (PSR) — Reference

## What are PSRs?

PSRs are PHP standards ratified by the PHP Framework Interoperability Group (PHP-FIG). They ensure that PHP code across different frameworks and libraries works together seamlessly.

## Active PSRs

| PSR | Title | Purpose |
|-----|-------|---------|
| **PSR-1** | Basic Coding Standard | Naming conventions, file structure |
| **PSR-3** | Logger Interface | Standard logging interface (Psr\Log) |
| **PSR-4** | Autoloading | Class file organization (replaces PSR-0) |
| **PSR-6** | Caching Interface | Cache pool and item interfaces |
| **PSR-7** | HTTP Message Interfaces | Request/response interfaces |
| **PSR-11** | Container Interface | Dependency injection container |
| **PSR-12** | Extended Coding Style | Beyond PSR-1 — braces, whitespace, imports |
| **PSR-13** | Hypermedia Links | Link relationships |
| **PSR-14** | Event Dispatcher | Event handling interfaces |
| **PSR-15** | HTTP Handlers | Request handlers and middleware |
| **PSR-16** | Simple Cache | Simple cache interface (alternative to PSR-6) |
| **PSR-17** | HTTP Factories | Factory for PSR-7 objects |
| **PSR-18** | HTTP Client | HTTP client interface |
| **PSR-20** | Clock | Clock interface |
| **PSR-22** | Clock (newer draft) | Updated clock interface |

## PSR-1 — Basic Coding Standard

```php
<?php

declare(strict_types=1);

// Files must use <?php or <?= tags
// Files must be UTF-8 without BOM

// Namespace and class declaration
namespace Vendor\Model;

// Class naming: StudlyCaps
class FooBar
{
    // Constants: all caps with underscore
    const VERSION = '1.0';

    // Method naming: camelCase
    public function doSomething(): void
    {
        // Property naming:
        // - $studlyCaps for class constants (implied)
        // - $camelCase for properties and methods
    }
}
```

## PSR-3 — Logger Interface

```php
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Psr\Log\AbstractLogger;

// Eight log levels (from RFC 5424)
$logger->emergency('System is down');
$logger->alert('Database unavailable');
$logger->critical('Unexpected error');
$logger->error('Something went wrong');
$logger->warning('Deprecated function used');
$logger->notice('User logged in');
$logger->info('Payment processed');
$logger->debug('Query executed: {sql}', ['sql' => 'SELECT ...']);

// Context array for structured data
$logger->info('User {user} created', [
    'user' => $user->getId(),
    'ip' => $_SERVER['REMOTE_ADDR'],
]);

// Interpolation — PSR-3 says placeholders like {key} are replaced
// from the context array

// NullLogger — useful for default/no-op logging
class MyService
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger()
    ) {}
}

// Custom logger extending AbstractLogger
class FileLogger extends AbstractLogger
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $interpolated = $this->interpolate($message, $context);
        file_put_contents(
            '/tmp/app.log',
            "[$level] $interpolated\n",
            FILE_APPEND
        );
    }

    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($message, $replace);
    }
}
```

## PSR-4 — Autoloading

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "App\\Tests\\": "tests/"
        }
    }
}
```

```php
// Vendor\Package\ClassName
//     → vendor/package/src/ClassName.php

// MyApp\Controllers\UserController
//     → src/Controllers/UserController.php (with "MyApp\\": "src/")

// Key rules:
// - Top-level namespace = Vendor\Package
// - Sub-namespace = directory structure
// - Class name = file name
// - Case-sensitive on case-sensitive filesystems
```

## PSR-6 — Caching Interface

```php
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

// Save to cache
$item = $pool->getItem('user_profile_' . $userId);
if (!$item->isHit()) {
    $profile = $userRepository->find($userId);
    $item->set($profile);
    $item->expiresAfter(3600);  // 1 hour
    $pool->save($item);
}

// Read from cache
$profile = $pool->getItem('user_profile_' . $userId)->get();

// Deferred save (batch)
$pool->saveDeferred($item);
// ... more items ...
$pool->commit();  // Flush all deferred items

// Delete
$pool->deleteItem('user_profile_' . $userId);
$pool->deleteItems(['key1', 'key2']);
$pool->clear();   // Clear entire pool
```

## PSR-7 — HTTP Messages

```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

// Request
$request->getMethod();                   // 'GET', 'POST'
$request->getUri();                      // UriInterface
$request->getHeaders();                  // All headers
$request->getHeaderLine('Content-Type'); // 'application/json'
$request->getBody();                     // StreamInterface
$request->getParsedBody();               // $_POST equivalent
$request->getQueryParams();              // $_GET equivalent
$request->getServerParams();
$request->getCookieParams();
$request->getUploadedFiles();            // UploadedFileInterface[]
$request->getAttributes();               // Custom attributes

// Response
$response->getStatusCode();              // 200, 404, etc.
$response->withStatus(404);              // Returns NEW instance (immutable)
$response->withHeader('Content-Type', 'application/json');
$response->withBody($stream);
$response->getBody()->write('Hello');

// Immutability — every modification returns a new instance
$response = $response->withHeader('X-Custom', 'value');
```

## PSR-11 — Container Interface

```php
use Psr\Container\ContainerInterface;

// Get a service
$logger = $container->get(LoggerInterface::class);

// Check if service exists
if ($container->has('App\Service\UserService')) {
    $service = $container->get('App\Service\UserService');
}

// Your container must implement:
interface ContainerInterface
{
    public function get(string $id): mixed;
    public function has(string $id): bool;
}
```

## PSR-12 — Extended Coding Style

```php
<?php

declare(strict_types=1);

namespace Vendor\Package;

use Vendor\Package\SomeClass;
use Vendor\Package\AnotherClass;
use Vendor\Package\YetAnotherClass;
use function Vendor\Package\someFunction;
use const Vendor\Package\SOME_CONSTANT;

class ClassName extends ParentClass implements Interface1, Interface2
{
    const SOME_CONSTANT = 'value';

    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function methodName(
        string $arg1,
        ?string $arg2 = null,
        int $arg3 = 0
    ): void {
        if ($expr1) {
            // if body
        } elseif ($expr2) {
            // elseif body
        } else {
            // else body
        }

        // Control structures: 1 space before brace
        // 'else' on same line as closing brace

        foreach ($collection as $key => $value) {
            // foreach body
        }

        try {
            // try body
        } catch (\Exception $e) {
            // catch body
        } finally {
            // finally body
        }

        // Closures
        $closure = function (string $arg): string {
            return $arg;
        };

        // Arrow functions on single line for simple cases
        $result = array_map(fn(int $n): int => $n * 2, $items);
    }

    // Abstract methods
    abstract protected function doSomething(): void;

    // Return type on same line as closing parenthesis
    final public static function getVersion(): string
    {
        return '1.0.0';
    }
}

// Anonymous classes
$instance = new class {};

// — Most keywords not in all caps: true, false, null
// — 4 spaces for indentation, no tabs
// — Line length: 120 soft limit, 80 recommended
// — One blank line between methods
// — Visibility declared on all properties and methods
```

## PSR-15 — HTTP Middleware

```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Middleware
class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Authenticator $auth
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!$this->auth->isAuthenticated($request)) {
            // Return early — unauthorized
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // Pass to next handler
        return $handler->handle($request);
    }
}

// Request handler (typically the application's router)
class Application implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Route and dispatch
        return $this->router->dispatch($request);
    }
}
```

## PSR-18 — HTTP Client

```php
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

$client = new GuzzleHttp\Client();  // Implements PSR-18

$request = $requestFactory->createRequest('POST', 'https://api.example.com/users');
$request = $request->withHeader('Content-Type', 'application/json');
$request = $request->withHeader('Authorization', 'Bearer ' . $token);

$body = $streamFactory->createStream(json_encode([
    'name' => 'Alice',
    'email' => 'alice@example.com',
]));
$request = $request->withBody($body);

$response = $client->sendRequest($request);
echo $response->getStatusCode();
echo $response->getBody()->getContents();
```

## PSR-20 / PSR-22 — Clock

```php
use Psr\Clock\ClockInterface;

class MyService
{
    public function __construct(
        private ClockInterface $clock
    ) {}

    public function isExpired(\DateTimeImmutable $expiresAt): bool
    {
        return $expiresAt < $this->clock->now();
    }
}
```

## Why PSRs Matter

```
Without PSRs:                     With PSRs:

$guzzle = new Guzzle();           $client = new GuzzleHttp\Client();
$guzzle->setUrl('...');           $request = $factory->createRequest('GET', '...');
$guzzle->setMethod('GET');        $response = $client->sendRequest($request);
$guzzle->addHeader('...');
$result = $guzzle->send();         // Any PSR-18 client works the same
                                   // Switch from Guzzle to Symfony HTTP Client
// Different library, different API   without changing calling code
```

## Checking PSR Compliance

```bash
# PHP CodeSniffer — check PSR-12
vendor/bin/phpcs --standard=PSR12 src/

# PHP-CS-Fixer — auto-fix PSR-12
vendor/bin/php-cs-fixer fix src/ --rules=@PSR12

# Composer — verify PSR-4 autoloading
composer dump-autoload -o
```
