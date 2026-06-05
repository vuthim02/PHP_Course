<?php

declare(strict_types=1);

namespace MicroFramework\Http;

class Response
{
    private int $statusCode;
    private array $headers;
    private string $body;
    private string $reasonPhrase;

    private const PHRASES = [
        100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK',
        201 => 'Created', 202 => 'Accepted', 204 => 'No Content',
        301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other',
        304 => 'Not Modified', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect',
        400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 405 => 'Method Not Allowed', 409 => 'Conflict',
        422 => 'Unprocessable Entity', 429 => 'Too Many Requests',
        500 => 'Internal Server Error', 502 => 'Bad Gateway',
        503 => 'Service Unavailable', 504 => 'Gateway Timeout',
    ];

    public function __construct(
        int    $statusCode = 200,
        string $body = '',
        array  $headers = [],
        ?string $reasonPhrase = null
    ) {
        $this->statusCode   = $statusCode;
        $this->body         = $body;
        $this->headers      = array_change_key_case($headers, CASE_LOWER);
        $this->reasonPhrase = $reasonPhrase ?? self::PHRASES[$statusCode] ?? 'Unknown';
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withStatus(int $code, ?string $reasonPhrase = null): static
    {
        $new = clone $this;
        $new->statusCode   = $code;
        $new->reasonPhrase = $reasonPhrase ?? self::PHRASES[$code] ?? 'Unknown';
        return $new;
    }

    public function withBody(string $body): static
    {
        $new       = clone $this;
        $new->body = $body;
        return $new;
    }

    public function withHeader(string $name, string $value): static
    {
        $new                 = clone $this;
        $new->headers[strtolower($name)] = $value;
        return $new;
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            statusCode: $status,
            body:       json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            headers:    ['content-type' => 'application/json']
        );
    }

    public static function redirect(string $url, int $status = 302): self
    {
        return new self(
            statusCode: $status,
            headers:    ['location' => $url]
        );
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;
    }
}
