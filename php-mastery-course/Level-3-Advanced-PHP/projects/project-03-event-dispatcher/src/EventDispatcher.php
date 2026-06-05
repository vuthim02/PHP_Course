<?php

declare(strict_types=1);

namespace EventDispatcher;

class EventDispatcher
{
    public function __construct(
        private readonly ListenerProvider $provider
    ) {}

    public function dispatch(Event $event): Event
    {
        $listeners = $this->provider->getListeners($event->getName());

        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }

    public function dispatchAsync(Event $event): void
    {
        $listeners = $this->provider->getListeners($event->getName());

        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            try {
                register_shutdown_function(fn() => $listener($event));
            } catch (\Throwable) {
                // Silently handle async dispatch failures
            }
        }
    }

    public function dispatchMultiple(Event ...$events): array
    {
        $results = [];
        foreach ($events as $event) {
            $results[] = $this->dispatch($event);
        }
        return $results;
    }

    public function map(string $eventName, array $data, callable $transformer): Event
    {
        $transformed = $transformer($data);
        return $this->dispatch(new Event($eventName, $transformed));
    }

    public function filter(string $eventName, mixed $value, callable $predicate): ?Event
    {
        if ($predicate($value)) {
            return $this->dispatch(new Event($eventName, $value));
        }
        return null;
    }
}
