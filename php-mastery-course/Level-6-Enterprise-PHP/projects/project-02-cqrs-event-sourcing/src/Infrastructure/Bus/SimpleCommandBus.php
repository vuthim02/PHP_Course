<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\Bus;

/**
 * SimpleCommandBus
 *
 * Dispatches commands to their handlers.
 * In a real system, this would support middleware (logging, transactions, etc.)
 * and be wired via a DI container.
 */
final class SimpleCommandBus
{
    /** @var array<string, callable> */
    private array $handlers = [];

    public function register(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(object $command): void
    {
        $commandClass = get_class($command);
        $handler = $this->handlers[$commandClass] ?? null;

        if ($handler === null) {
            throw new \RuntimeException("No handler registered for command: {$commandClass}");
        }

        $handler($command);
    }
}
