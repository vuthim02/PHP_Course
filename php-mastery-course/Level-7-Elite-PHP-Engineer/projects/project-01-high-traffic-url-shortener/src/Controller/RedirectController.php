<?php

declare(strict_types=1);

namespace UrlShortener\Controller;

use UrlShortener\Service\UrlService;
use UrlShortener\Service\AnalyticsService;
use UrlShortener\Middleware\CacheMiddleware;

final class RedirectController
{
    public function __construct(
        private readonly UrlService $urlService,
        private readonly AnalyticsService $analyticsService,
        private readonly CacheMiddleware $cacheMiddleware
    ) {}

    public function __invoke(string $shortCode): void
    {
        // Record analytics asynchronously (non-blocking)
        $this->analyticsService->recordClick(
            $shortCode,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_REFERER'] ?? ''
        );

        $url = $this->urlService->resolve($shortCode);

        if ($url === null) {
            http_response_code(404);
            echo json_encode(['error' => 'URL not found']);
            return;
        }

        $headers = $this->cacheMiddleware->getRedirectHeaders($shortCode);
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // 301 for permanent redirects, 302 for temporary/custom aliases
        $statusCode = strlen($shortCode) >= 6 ? 301 : 302;
        http_response_code($statusCode);
        header("Location: {$url}");
    }
}
