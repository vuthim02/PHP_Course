<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode;
    private array $headers;
    private mixed $body;

    public function __construct(mixed $body = null, int $statusCode = 200, array $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'application/json',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ], $headers);
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        if ($this->body !== null) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        return new self($data, $status, $headers);
    }

    public static function success(mixed $data, int $status = 200, array $extra = []): self
    {
        $body = array_merge(['success' => true, 'data' => $data], $extra);
        return new self($body, $status);
    }

    public static function error(string $message, int $status = 400, array $errors = []): self
    {
        $body = ['success' => false, 'message' => $message];
        if (!empty($errors)) {
            $body['errors'] = $errors;
        }
        return new self($body, $status);
    }

    public static function paginated(array $items, int $total, int $page, int $perPage, string $baseUrl): self
    {
        $lastPage = (int) ceil($total / $perPage);

        $links = [
            'self' => "{$baseUrl}?page={$page}&per_page={$perPage}",
            'first' => "{$baseUrl}?page=1&per_page={$perPage}",
            'last' => "{$baseUrl}?page={$lastPage}&per_page={$perPage}",
        ];

        if ($page > 1) {
            $links['prev'] = "{$baseUrl}?page=" . ($page - 1) . "&per_page={$perPage}";
        }
        if ($page < $lastPage) {
            $links['next'] = "{$baseUrl}?page=" . ($page + 1) . "&per_page={$perPage}";
        }

        return self::success($items, 200, [
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
            'links' => $links,
        ]);
    }

    public static function created(mixed $data): self
    {
        return self::success($data, 201);
    }

    public static function noContent(): self
    {
        return new self(null, 204);
    }
}
