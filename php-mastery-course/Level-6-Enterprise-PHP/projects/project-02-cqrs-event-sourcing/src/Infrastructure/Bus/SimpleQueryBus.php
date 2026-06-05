<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\Bus;

/**
 * SimpleQueryBus
 *
 * Dispatches queries to their handlers.
 * In CQRS, the query side is completely separate from the command side.
 * Queries return data without side effects.
 */
final class SimpleQueryBus
{
    /** @var array<string, callable> */
    private array $handlers = [];

    public function register(string $queryClass, callable $handler): void
    {
        $this->handlers[$queryClass] = $handler;
    }

    public function dispatch(object $query): mixed
    {
        $queryClass = get_class($query);
        $handler = $this->handlers[$queryClass] ?? null;

        if ($handler === null) {
            throw new \RuntimeException("No handler registered for query: {$queryClass}");
        }

        return $handler($query);
    }
}
