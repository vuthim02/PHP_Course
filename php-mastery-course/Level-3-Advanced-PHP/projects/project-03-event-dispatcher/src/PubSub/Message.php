<?php

declare(strict_types=1);

namespace EventDispatcher\PubSub;

class Message
{
    private array $headers = [];
    private bool $handled = false;

    public function __construct(
        private readonly string $topic,
        private readonly mixed $payload = null,
        private ?string $id = null
    ) {
        $this->id ??= bin2hex(random_bytes(16));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function markHandled(): void
    {
        $this->handled = true;
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }
}
