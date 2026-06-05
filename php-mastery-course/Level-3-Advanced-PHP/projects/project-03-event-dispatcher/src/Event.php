<?php

declare(strict_types=1);

namespace EventDispatcher;

class Event
{
    private bool $propagationStopped = false;
    private array $context = [];

    public function __construct(
        private readonly string $name,
        private readonly mixed $data = null
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function setContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function getContext(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }
}
