<?php

declare(strict_types=1);

namespace MicroFramework\Http;

class Request
{
    private array $attributes = [];
    private ?string $body = null;

    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $headers = [],
        private readonly array $query = [],
        private readonly array $post = [],
        private readonly array $cookies = [],
        private readonly array $files = [],
        private readonly array $server = [],
    ) {}

    public static function fromGlobals(): static
    {
        $body = null;
        $rawBody = file_get_contents('php://input');
        if ($rawBody !== false && $rawBody !== '') {
            $body = $rawBody;
        }

        $request = new static(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri:    $_SERVER['REQUEST_URI'] ?? '/',
            headers: getallheaders() ?: [],
            query:  $_GET,
            post:   $_POST,
            cookies: $_COOKIE,
            files:  $_FILES,
            server: $_SERVER,
        );

        if ($body !== null) {
            $request->body = $body;
        }

        return $request;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        $path = parse_url($this->uri, PHP_URL_PATH);
        return $path !== false && $path !== null ? $path : '/';
    }

    public function getHeader(string $name, ?string $default = null): ?string
    {
        return $this->headers[$name] ?? $default;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getQuery(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function getPost(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getParsedBody(): array
    {
        if ($this->body !== null) {
            $decoded = json_decode($this->body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            parse_str($this->body, $result);
            return $result;
        }
        return $this->post;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withMethod(string $method): static
    {
        return new static(
            method: $method, uri: $this->uri, headers: $this->headers,
            query: $this->query, post: $this->post, cookies: $this->cookies,
            files: $this->files, server: $this->server
        );
    }
}
