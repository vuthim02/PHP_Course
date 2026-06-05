<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $queryParams;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;
    private ?string $routeParam;

    public function __construct()
    {
        $this->queryParams = $_GET;
        $this->body = [];
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
        $this->routeParam = null;
        $this->parseBody();
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
        }
        return $headers;
    }

    private function parseBody(): void
    {
        $contentType = $this->getHeader('Content-Type', '');

        if (in_array($this->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (str_contains($contentType, 'application/json')) {
                $raw = file_get_contents('php://input');
                $this->body = json_decode($raw, true) ?? [];
            } elseif (str_contains($contentType, 'multipart/form-data')) {
                $this->body = $_POST;
            } else {
                parse_str(file_get_contents('php://input'), $this->body);
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD']);
    }

    public function getPath(): string
    {
        $path = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return '/' . trim($path, '/');
    }

    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function getFile(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function getHeader(string $key, ?string $default = null): ?string
    {
        $key = strtoupper(str_replace('-', '_', $key));
        // Also try HTTP_ prefix
        $httpKey = 'HTTP_' . $key;
        return $this->headers[$key] ?? $this->headers[$httpKey] ?? $default;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBearerToken(): ?string
    {
        $auth = $this->getHeader('Authorization');
        if ($auth && str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

    public function setRouteParam(?string $param): void
    {
        $this->routeParam = $param;
    }

    public function getRouteParam(): ?string
    {
        return $this->routeParam;
    }

    public function getClientIp(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function expectsJson(): bool
    {
        $accept = $this->getHeader('Accept', '');
        return str_contains($accept, 'application/json');
    }
}
