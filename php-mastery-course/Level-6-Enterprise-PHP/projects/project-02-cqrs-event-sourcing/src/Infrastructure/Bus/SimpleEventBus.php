<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\Bus;

/**
 * SimpleEventBus
 *
 * Dispatches events to registered projectors/read model updaters.
 * After events are stored, they are published so projections can update.
 */
final class SimpleEventBus
{
    /** @var array<string, callable[]> */
    private array $listeners = [];

    public function subscribe(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function publish(object $event): void
    {
        $eventClass = get_class($event);
        $listeners = $this->listeners[$eventClass] ?? [];

        foreach ($listeners as $listener) {
            call_user_func($listener, $event);
        }
    }

    public function publishAll(array $events): void
    {
        foreach ($events as $event) {
            $this->publish($event);
        }
    }
}
