<?php

declare(strict_types=1);

namespace DDD\Infrastructure\Event;

/**
 * InMemoryEventBus
 *
 * Simple event bus that dispatches domain events to registered listeners.
 * In production this would be replaced with RabbitMQ, Kafka, or Symfony EventDispatcher.
 * Demonstrates the event-driven communication pattern between bounded contexts.
 */
final class InMemoryEventBus
{
    /** @var array<string, callable[]> */
    private array $listeners = [];

    /**
     * Register a listener for a specific event class.
     */
    public function subscribe(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Dispatch an event to all registered listeners.
     */
    public function dispatch(object $event): void
    {
        $eventClass = get_class($event);
        $listeners = $this->listeners[$eventClass] ?? [];

        foreach ($listeners as $listener) {
            call_user_func($listener, $event);
        }
    }

    /**
     * Dispatch all events recorded on an aggregate.
     */
    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
