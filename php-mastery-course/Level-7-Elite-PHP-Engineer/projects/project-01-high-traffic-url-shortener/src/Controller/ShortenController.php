<?php

declare(strict_types=1);

namespace UrlShortener\Controller;

use UrlShortener\Service\UrlService;
use UrlShortener\Middleware\RateLimitMiddleware;

final class ShortenController
{
    public function __construct(
        private readonly UrlService $urlService,
        private readonly RateLimitMiddleware $rateLimiter
    ) {}

    public function __invoke(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$this->rateLimiter->isAllowed($ip)) {
            http_response_code(429);
            echo json_encode([
                'error' => 'Rate limit exceeded',
                'retry_after' => 60,
                'remaining' => 0,
            ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['url'])) {
            http_response_code(400);
            echo json_encode(['error' => 'URL is required']);
            return;
        }

        try {
            $result = $this->urlService->shorten(
                $input['url'],
                $input['custom_alias'] ?? null
            );

            http_response_code(201);
            echo json_encode($result);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
