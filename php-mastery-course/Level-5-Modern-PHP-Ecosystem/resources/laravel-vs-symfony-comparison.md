# Laravel vs Symfony — Framework Comparison

## Philosophy

| Aspect | Laravel | Symfony |
|--------|---------|---------|
| **Motto** | "The PHP Framework for Web Artisans" | "High Performance PHP Framework for Web Development" |
| **Approach** | Convention over configuration | Explicit configuration |
| **Learning curve** | Gentle — great for beginners | Steeper — more concepts upfront |
| **Flexibility** | Opinionated, but customizable | Highly modular, swap anything |
| **Best for** | Rapid development, MVP, startups | Enterprise, complex business logic |

## Core Architecture

```php
// Laravel — service container
$userService = app(UserService::class);

// Symfony — dependency injection
// services.yaml:
// App\Service\UserService: ~
$userService = $container->get(UserService::class);
```

### Routing

```php
// Laravel (routes/web.php)
Route::get('/users', [UserController::class, 'index']);
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// Symfony (config/routes.yaml)
users_list:
    path: /users
    controller: App\Controller\UserController::index
    methods: GET
```

### ORM

```php
// Laravel Eloquent
$users = User::where('active', true)
    ->with('posts')
    ->orderBy('name')
    ->paginate(20);

$user = User::findOrFail($id);
$user->update(['name' => 'Alice']);

// Symfony Doctrine
$users = $this->entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from(User::class, 'u')
    ->where('u.active = :active')
    ->setParameter('active', true)
    ->getQuery()
    ->getResult();

$user = $this->entityManager->find(User::class, $id);
$user->setName('Alice');
$this->entityManager->flush();
```

### Templating

```php
// Laravel Blade
@extends('layouts.app')
@section('content')
    <h1>{{ $title }}</h1>
    @foreach ($users as $user)
        <p>{{ $user->name }}</p>
    @endforeach
@endsection

// Symfony Twig
{% extends 'base.html.twig' %}
{% block content %}
    <h1>{{ title }}</h1>
    {% for user in users %}
        <p>{{ user.name }}</p>
    {% endfor %}
{% endblock %}
```

## Feature Comparison

| Feature | Laravel | Symfony |
|---------|---------|---------|
| **Auth** | Built-in (scaffold, Breeze, Jetstream, Sanctum) | SecurityBundle + Guard authenticators |
| **Admin** | Nova, Filament, Voyager | EasyAdmin, SonataAdmin |
| **API** | Sanctum, Passport, API Resources | API Platform, FOSRestBundle |
| **Testing** | PHPUnit + built-in helpers | PHPUnit + Panther, WebTestCase |
| **ORM** | Eloquent (Active Record) | Doctrine (Data Mapper) |
| **CLI** | Artisan | Console (Symfony Console) |
| **Mail** | Mail facade + Mailable classes | Mailer component |
| **Queue** | Queue system + Horizon | Messenger component |
| **Events** | Event system + listeners | EventDispatcher component |
| **Cache** | Cache facade | Cache component |
| **Task Scheduler** | Artisan schedule | Cron + Messenger |
| **File Storage** | Flysystem integration | Flysystem via League\Flysystem |
| **Logging** | Monolog integration | Monolog integration |
| **DB Migrations** | Built-in migrations | DoctrineMigrationsBundle |
| **Form Builder** | FormRequest + validation | Form component |
| **Asset Compilation** | Vite | Webpack Encore / Vite |

## Performance Benchmarks

| Benchmark | Laravel | Symfony |
|-----------|---------|---------|
| Requests/sec (hello world) | ~180 | ~220 |
| Requests/sec (full stack) | ~80 | ~100 |
| Memory per request | ~15MB | ~12MB |
| Boot time | ~50ms | ~40ms |

**Note:** Both are fast enough for 95% of applications. Performance differences matter only at very high scale.

## Ecosystem

### Laravel Ecosystem
- **Forge** — server management
- **Vapor** — serverless deployment (AWS Lambda)
- **Envoyer** — zero-downtime deployment
- **Nova** — admin panel
- **Spark** — subscription billing
- **Cashier** — Stripe/Paddle billing (open source)
- **Socialite** — OAuth social login
- **Telescope** — debugging assistant
- **Horizon** — queue monitoring
- **Sanctum** — API token auth
- **Passport** — OAuth2 server
- **Echo** — real-time broadcasting
- **Breeze / Jetstream** — auth scaffolding + Teams
- **Laracasts** — video tutorials (paid)
- **Laravel News** — community news site

### Symfony Ecosystem
- **Symfony Cloud** — platform.sh deployment
- **Panther** — browser testing
- **API Platform** — API-first framework
- **EasyAdmin** — admin generator
- **Sonata** — admin/project bundles
- **Sylius** — e-commerce framework
- **MakerBundle** — code generation
- **DebugBundle** — debugging toolbar
- **WebProfiler** — profiler bar
- **Translator** — i18n
- **Form** — form builder
- **Validator** — validation component
- **Workflow** — state machine
- **SensioFrameworkExtraBundle** — annotations
- **SymfonyCast** — video tutorials (paid)

## Community

| Metric | Laravel | Symfony |
|--------|---------|---------|
| GitHub stars | ~78k | ~30k |
| Contributors | ~4,000 | ~25,000 |
| Packages (Packagist) | ~5,500 (Laravel-specific) | ~15,000 (Symfony + bundles) |
| Conferences | Laracon (US, EU, AU) | SymfonyCon, SymfonyLive |
| Job market | Very strong (startups, agencies) | Strong (enterprise, EU) |

## When to Choose Which

### Choose Laravel when:
- Building a startup or MVP quickly
- Team prefers convention over configuration
- Need out-of-the-box solutions for common tasks
- Building a monolith with simple scaling needs
- Working with primarily Laravel developers

### Choose Symfony when:
- Building enterprise applications with complex business logic
- Need fine-grained control over architecture
- Components must be reusable across projects
- Working with multiple applications sharing bundles
- Need long-term support (LTS versions)
- Building public APIs (API Platform)

## Hybrid Approach — Use Components Separately

```bash
# Use Symfony components in Laravel
composer require symfony/console
composer require symfony/process
composer require symfony/mailer
composer require symfony/translation

# Use Laravel components in Symfony
composer require illuminate/database    # Eloquent ORM standalone
composer require illuminate/redis       # Redis standalone
composer require illuminate/mail        # Mail standalone
```

## Decision Matrix

| Factor | Weight for Laravel | Weight for Symfony |
|--------|--------------------|--------------------|
| Development speed | High | Medium |
| Flexibility | Medium | High |
| Learning curve | Low | Medium-High |
| Documentation quality | High | High |
| Package ecosystem | Medium-High | High |
| Long-term stability | Medium (yearly releases) | High (LTS every 2 years) |
| Job opportunities (global) | Higher | Higher in EU |
| Enterprise features | Medium | High |
| Performance | Good | Slightly better |
| Upgrade between versions | Moderate | Well-documented |

## Getting Started

```bash
# Laravel
composer create-project laravel/laravel my-app
php artisan serve

# Symfony
composer create-project symfony/skeleton my-app
cd my-app && symfony serve
```
