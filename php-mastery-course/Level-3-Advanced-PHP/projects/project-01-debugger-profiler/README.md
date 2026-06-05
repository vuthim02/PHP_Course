# PHP Debugger & Profiler Tool

An advanced debugging and profiling toolkit for PHP applications featuring execution time tracking, memory usage monitoring, SQL query logging with EXPLAIN, stack trace viewer, variable dumper, and a custom error handler.

## Advanced PHP Concepts Demonstrated

| Concept | File |
|---|---|
| **Late Static Binding** | `src/Debugger.php:17` — Singleton pattern using `static::$instance` |
| **Reflection API** | `src/Dump.php:61-80` — Reflects objects to inspect properties, modifiers, and values |
| **Anonymous Closures** | `src/ErrorHandler.php:29-39` — Closures passed to `set_error_handler` / `set_exception_handler` |
| **Output Buffering** | `src/Debugger.php:41` — `ob_start()` captures output for toolbar injection |
| **Advanced Error Handling** | `src/ErrorHandler.php` — Custom error/exception/shutdown handlers with error suppression awareness |
| **SPL Types / Arrays** | `src/collectors/*.php` — Typed property arrays for structured data collection |
| **Static Methods as Utility** | `src/Debugger.php:97-117` — Static `dd()` for dump-and-die debugging |
| **Callable Type System** | `src/Profiler.php:82-96` — `measure()` accepts `callable` for isolating code performance |

## Features

- **Execution Time Tracker** — Marks and measures code execution with microsecond precision
- **Memory Usage Monitor** — Snapshots memory at any point, tracks peak usage
- **SQL Query Logger** — Logs queries with bindings, duration, and automatic `EXPLAIN` on SELECTs
- **Variable Dumper** — Recursively dumps arrays/objects with syntax highlighting
- **Debug Toolbar** — Auto-injects a bottom toolbar with execution stats
- **Error Handler** — Catches all errors/exceptions and renders a beautiful debug page
- **Benchmarking Utility** — `Debugger::benchmark()` measures callable performance over iterations
- **Singleton Pattern** — All major classes use LSB-safe singletons

## Setup

```bash
composer install
```

## Run Demo

```bash
php examples/demo.php
```

## Usage

```php
<?php

require_once 'vendor/autoload.php';

use DebuggerProfiler\Debugger;
use DebuggerProfiler\Profiler;

// Enable the debugger
Debugger::getInstance()->enable();

// Profile a block
Profiler::measure(function () {
    $result = array_map(fn($n) => $n * 2, range(1, 1000));
    Debugger::getInstance()->log($result, 'doubled');
}, 'map_double');

// Mark time points
Profiler::getInstance()->time()->mark('db_query');
// ... run query ...
Profiler::getInstance()->time()->mark('db_query_done');

// Memory snapshot
Profiler::getInstance()->memory()->snapshot('after_processing');

// Log a query
Profiler::getInstance()->queries()->log('SELECT * FROM users WHERE id = ?', [42], 0.0021);

// Dump and die
Debugger::dd($_GET, $_POST);

// Benchmark a function
$results = Debugger::benchmark(fn() => json_decode('{"key":"value"}'), 5000);
print_r($results);
```

## What Makes It "Advanced PHP"

This project goes beyond basic error handling by combining **late static binding** (enabling extensible singletons), **Reflection API** (dynamic object introspection for variable dump), **output buffering** (non-intrusive toolbar injection), **first-class closures** (error handler registration), and **comprehensive type safety** (strict types, typed properties, union types). The profiler integrates multiple collectors into a unified report, demonstrating how to build developer tooling that would be impossible in older PHP versions.
