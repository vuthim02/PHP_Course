# Chapter 9: Laravel Packages

## Learning Objectives

By the end of this chapter you will:
- Scaffold a Laravel package from scratch
- Register service providers, facades, and config
- Publish migrations, routes, views, and assets
- Write package tests using Orchestra Testbench
- Publish and version a package on Packagist

---

## 9.1 Package Structure

```
my-package/
├── src/
│   ├── Commands/
│   │   └── MyPackageCommand.php
│   ├── Facades/
│   │   └── MyPackage.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── MyPackageController.php
│   │   ├── Middleware/
│   │   │   └── MyPackageMiddleware.php
│   │   └── Requests/
│   │       └── MyPackageRequest.php
│   ├── Migrations/
│   │   └── 2024_01_01_000001_create_my_package_tables.php
│   ├── Models/
│   │   └── MyPackageModel.php
│   ├── Resources/
│   │   └── views/
│   │       └── dashboard.blade.php
│   ├── routes/
│   │   └── web.php
│   ├── MyPackageServiceProvider.php
│   └── MyPackage.php
├── config/
│   └── my-package.php
├── database/
│   └── factories/
│       └── MyPackageModelFactory.php
├── tests/
│   ├── Feature/
│   │   └── MyPackageTest.php
│   └── OrchestraTestCase.php
├── composer.json
├── README.md
├── LICENSE.md
└── .gitignore
```

### composer.json

```json
{
    "name": "vendor/my-package",
    "description": "A description of your package",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "illuminate/support": "^11.0|^12.0",
        "illuminate/contracts": "^11.0|^12.0"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0",
        "phpunit/phpunit": "^11.0",
        "mockery/mockery": "^1.6"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\MyPackage\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Vendor\\MyPackage\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Vendor\\MyPackage\\MyPackageServiceProvider"
            ],
            "aliases": {
                "MyPackage": "Vendor\\MyPackage\\Facades\\MyPackage"
            }
        }
    },
    "config": {
        "sort-packages": true
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

---

## 9.2 Service Provider

```php
<?php
namespace Vendor\MyPackage;

use Illuminate\Support\ServiceProvider;

class MyPackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config so user defaults apply
        $this->mergeConfigFrom(
            __DIR__.'/../config/my-package.php', 'my-package'
        );

        // Bind the main class
        $this->app->singleton('my-package', function ($app) {
            return new MyPackage(
                config('my-package.api_key'),
                config('my-package.timeout', 30)
            );
        });

        // Bind an interface to implementation
        $this->app->bind(
            Contracts\PaymentGateway::class,
            Services\StripeGateway::class
        );

        // Register commands
        $this->commands([
            Commands\MyPackageCommand::class,
            Commands\InstallCommand::class,
        ]);
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/my-package.php' => config_path('my-package.php'),
        ], 'my-package-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'my-package-migrations');

        // Or load migrations automatically
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'my-package');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/my-package'),
        ], 'my-package-views');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'my-package');

        // Publish translations
        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/my-package'),
        ], 'my-package-lang');

        // Load factories (Laravel 11+)
        $this->loadFactoriesFrom(__DIR__.'/../database/factories');

        // Register blade components
        \Illuminate\Support\Facades\Blade::component(
            'my-package::alert', Components\Alert::class
        );

        // Register middleware in router
        $router = $this->app['router'];
        $router->aliasMiddleware('my-package.auth', Http\Middleware\Authenticate::class);
        $router->pushMiddlewareToGroup('api', Http\Middleware\ThrottleApi::class);
    }
}
```

---

## 9.3 Package Config

```php
<?php
// config/my-package.php
return [
    'api_key' => env('MY_PACKAGE_API_KEY'),
    'api_url' => env('MY_PACKAGE_API_URL', 'https://api.example.com/v1'),
    'timeout' => env('MY_PACKAGE_TIMEOUT', 30),
    'features' => [
        'webhook' => env('MY_PACKAGE_WEBHOOK_ENABLED', true),
        'logging' => env('MY_PACKAGE_LOGGING', false),
    ],
    'cache' => [
        'ttl' => env('MY_PACKAGE_CACHE_TTL', 3600),
        'prefix' => 'my_package_',
    ],
    'tables' => [
        'subscriptions' => 'my_package_subscriptions',
        'transactions' => 'my_package_transactions',
    ],
];
```

---

## 9.4 Main Package Class and Facade

```php
<?php
namespace Vendor\MyPackage;

class MyPackage
{
    private \GuzzleHttp\Client $http;

    public function __construct(
        private string $apiKey,
        private int $timeout,
        ?\GuzzleHttp\Client $client = null,
    ) {
        $this->http = $client ?? new \GuzzleHttp\Client([
            'base_uri' => config('my-package.api_url'),
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function createSubscription(string $email, string $plan): Subscription
    {
        $response = $this->http->post('/subscriptions', [
            'json' => ['email' => $email, 'plan' => $plan],
        ]);
        return new Subscription(json_decode($response->getBody(), true));
    }

    public function cancelSubscription(string $id): bool
    {
        return $this->http->delete("/subscriptions/{$id}")->getStatusCode() === 200;
    }

    public function getSubscription(string $id): ?Subscription
    {
        $response = $this->http->get("/subscriptions/{$id}");
        if ($response->getStatusCode() === 404) return null;
        return new Subscription(json_decode($response->getBody(), true));
    }
}
```

```php
<?php
namespace Vendor\MyPackage\Facades;

use Illuminate\Support\Facades\Facade;

class MyPackage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'my-package';
    }
}
```

---

## 9.5 Blade Components

```php
<?php
namespace Vendor\MyPackage\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public function __construct(
        public string $type = 'info',
        public ?string $title = null,
    ) {}

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('my-package::components.alert');
    }
}
```

```blade
{{-- resources/views/components/alert.blade.php --}}
<div {{ $attributes->merge(['class' => 'px-4 py-3 rounded border']) }}>
    @if($title)
        <h3 class="font-bold">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
```

---

## 9.6 Package Routes

```php
<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Vendor\MyPackage\Http\Controllers\DashboardController;
use Vendor\MyPackage\Http\Controllers\SubscriptionController;

Route::group([
    'prefix' => 'my-package',
    'middleware' => ['web', 'my-package.auth'],
    'as' => 'my-package.',
], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe');
    Route::delete('/subscribe/{id}', [SubscriptionController::class, 'destroy'])->name('unsubscribe');
});
```

---

## 9.7 Testing with Orchestra Testbench

```php
<?php
// tests/OrchestraTestCase.php
namespace Vendor\MyPackage\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\MyPackage\MyPackageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [MyPackageServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['MyPackage' => \Vendor\MyPackage\Facades\MyPackage::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('my-package.api_key', 'test_key');
        $app['config']->set('my-package.api_url', 'https://api.test.com/v1');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineRoutes($router): void
    {
        $router->get('my-package/test', function () {
            return 'test route';
        });
    }
}
```

```php
<?php
// tests/Feature/MyPackageTest.php
namespace Vendor\MyPackage\Tests\Feature;

use Vendor\MyPackage\Tests\TestCase;
use Vendor\MyPackage\Facades\MyPackage;
use Illuminate\Support\Facades\Http;

class MyPackageTest extends TestCase
{
    public function test_facade_resolves_service(): void
    {
        $this->assertInstanceOf(
            \Vendor\MyPackage\MyPackage::class,
            MyPackage::getFacadeRoot()
        );
    }

    public function test_create_subscription(): void
    {
        Http::fake([
            'api.test.com/v1/subscriptions' => Http::response([
                'id' => 'sub_123', 'email' => 'test@test.com', 'plan' => 'pro',
            ], 201),
        ]);

        $subscription = MyPackage::createSubscription('test@test.com', 'pro');

        $this->assertEquals('sub_123', $subscription->id);
        $this->assertEquals('pro', $subscription->plan);
    }

    public function test_config_is_publishable(): void
    {
        $this->assertEquals('test_key', config('my-package.api_key'));
    }

    public function test_route_exists(): void
    {
        $this->get('my-package/test')->assertOk()->assertSee('test route');
    }
}
```

### PHPUnit Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Package Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_KEY" value="base64:testkey12345678901234567890123456789012"/>
        <env name="DB_CONNECTION" value="testing"/>
    </php>
</phpunit>
```

---

## 9.8 Publishing to Packagist

```bash
git tag v1.0.0
git push origin v1.0.0

# Submit at https://packagist.org/packages/submit

# Install in any project:
composer require vendor/my-package

# Local development:
composer config repositories.my-package path ./packages/my-package
composer require vendor/my-package:@dev
```

### Version Strategy

| Stability | Constraint | Example |
|-----------|-----------|---------|
| Stable    | `^1.0`    | `^1.2.3` |
| Dev       | `dev-main` | `dev-feature-x` |

---

## 9.9 Package Installation Command

```php
<?php
namespace Vendor\MyPackage\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'my-package:install {--force : Overwrite existing files}';

    protected $description = 'Install my-package';

    public function handle(): int
    {
        $this->info('Installing MyPackage...');

        $this->call('vendor:publish', [
            '--provider' => "Vendor\\MyPackage\\MyPackageServiceProvider",
            '--tag' => 'my-package-config',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--provider' => "Vendor\\MyPackage\\MyPackageServiceProvider",
            '--tag' => 'my-package-migrations',
        ]);

        $apiKey = $this->secret('What is your API key?');

        $envPath = $this->laravel->environmentFilePath();
        file_put_contents($envPath, "MY_PACKAGE_API_KEY={$apiKey}\n", FILE_APPEND);

        $this->info('MyPackage installed successfully!');

        return self::SUCCESS;
    }
}
```

---

## 9.10 Exercises

1. Create a package scaffold with service provider, config, facade
2. Add migratable model with a factory and a seeder
3. Write blade components and publishable views
4. Test the package with Orchestra Testbench
5. Publish a package to Packagist and install in a Laravel project

---

## 9.11 Interview Questions

1. "How does Laravel auto-discover packages?"
2. "What's the difference between `mergeConfigFrom` and `publishes`?"
3. "How would you test a package without a Laravel app?"
4. "Explain the `loadViewsFrom` vs view publishing pattern."
5. "How does Orchestra Testbench simulate a full Laravel application?"

---

## Further Reading

- **Doc:** [Laravel Package Development](https://laravel.com/docs/packages)
- **Doc:** [Orchestra Testbench](https://github.com/orchestral/testbench)
- **Doc:** [Packagist](https://packagist.org/)
- **Tutorial:** [Laravel Package Training](https://laravelpackage.com/)

---

*End of Chapter 9. Proceed to Chapter 10: Symfony Framework.*
