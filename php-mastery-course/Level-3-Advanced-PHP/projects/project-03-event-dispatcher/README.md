# Event Dispatcher & Pub-Sub System

An event-driven architecture toolkit featuring a priority-based event dispatcher with wildcard support, subscriber interface, async dispatching, and a publish-subscribe message bus with middleware pipeline.

## Advanced PHP Concepts Demonstrated

| Concept | File |
|---|---|
| **Closures (Anonymous Functions)** | `examples/events-demo.php:18-30` — Listeners registered as closures |
| **Anonymous Classes** | `src/PubSub/MessageBus.php:24-33` — Wraps callbacks into `HandlerInterface` |
| **SPL SplPriorityQueue** | `src/ListenerProvider.php:23-27` — Priority-ordered listener storage |
| **Functional Programming** | `src/EventDispatcher.php:56-70` — `map()` / `filter()` methods applying closures to events |
| **Callable Type System** | `src/PubSub/HandlerInterface.php` — `__invoke` for treating objects as callables |
| **First-Class Callables** | `src/PubSub/MessageBus.php` — Middleware pipeline using composable closures |
| **Variadic Arguments** | `src/EventDispatcher.php:44-53` — `dispatchMultiple(Event ...$events)` |
| **Generator-free iteration** | `src/ListenerProvider.php:60-68` — Cloning SplPriorityQueue for iteration |

## Features

- **Priority-based Event Dispatching** — Listeners with numeric priority using `SplPriorityQueue`
- **Wildcard Event Matching** — Subscribe to `user.*` and match `user.registered`, `user.deleted`, etc.
- **Propagation Stopping** — `$event->stopPropagation()` prevents further listeners
- **Subscriber Interface** — Class-based event subscription
- **Async Dispatching** — `dispatchAsync()` via `register_shutdown_function`
- **Functional Event API** — `map()` and `filter()` for event-driven data transformation
- **Pub-Sub Message Bus** — Topic-based messaging with `Message` objects
- **Middleware Pipeline** — Add middleware to the bus for logging, validation, etc.
- **Anonymous Class Handlers** — Wrap callbacks into typed handlers

## Setup

```bash
composer install
```

## Run Demo

```bash
php examples/events-demo.php
```

## Usage

```php
<?php

use EventDispatcher\Event;
use EventDispatcher\EventDispatcher;
use EventDispatcher\ListenerProvider;
use EventDispatcher\PubSub\Message;
use EventDispatcher\PubSub\MessageBus;

$provider = new ListenerProvider();
$dispatcher = new EventDispatcher($provider);

// Add listener with priority
$provider->addListener('user.registered', function (Event $e) {
    echo "User: " . $e->getData()['email'];
}, 10);

// Wildcard listener
$provider->addListener('user.*', function (Event $e) {
    echo "Caught: " . $e->getName();
});

// Dispatch
$dispatcher->dispatch(new Event('user.registered', ['email' => 'a@b.com']));

// Pub-Sub
$bus = new MessageBus();
$bus->subscribeCallback('orders.created', function (Message $msg) {
    echo "Order: " . $msg->getPayload()['id'];
});
$bus->publish(new Message('orders.created', ['id' => 1]));
```

## What Makes It "Advanced PHP"

This project demonstrates **closures as first-class citizens** (listeners, middleware, transformers), **anonymous classes** (wrapping callbacks into typed contracts), **SPL data structures** (`SplPriorityQueue` for ordered dispatch), and **functional programming patterns** (`map`/`filter` on events). The **middleware pipeline** shows functional composition of closure-based middleware. The wildcard system uses **regular expressions** for pattern matching event names. This architecture mirrors real-world frameworks like Symfony's EventDispatcher and Laravel's event system.
