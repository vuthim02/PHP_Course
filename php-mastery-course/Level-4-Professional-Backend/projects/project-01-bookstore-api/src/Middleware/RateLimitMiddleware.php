<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\RateLimiter;

class RateLimitMiddleware
{
    private RateLimiter $limiter;

    public function __construct()
    {
        $this->limiter = new RateLimiter();
    }

    public function process(Request $request, callable $next): Response
    {
        $ip = $request->getClientIp();
        $key = "rate_limit:{$ip}";

        $result = $this->limiter->check($key);

        if (!$result['allowed']) {
            $response = Response::error('Rate limit exceeded. Try again in ' . $result['retry_after'] . ' seconds.', 429);
            $response->setHeader('X-RateLimit-Limit', (string) $result['limit']);
            $response->setHeader('X-RateLimit-Remaining', '0');
            $response->setHeader('X-RateLimit-Reset', (string) $result['reset_at']);
            $response->setHeader('Retry-After', (string) $result['retry_after']);
            return $response;
        }

        $response = $next($request);

        $response->setHeader('X-RateLimit-Limit', (string) $result['limit']);
        $response->setHeader('X-RateLimit-Remaining', (string) $result['remaining']);
        $response->setHeader('X-RateLimit-Reset', (string) $result['reset_at']);

        return $response;
    }
}
