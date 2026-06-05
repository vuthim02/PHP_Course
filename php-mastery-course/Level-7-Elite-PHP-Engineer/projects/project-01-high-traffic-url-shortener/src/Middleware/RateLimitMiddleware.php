<?php

declare(strict_types=1);

namespace UrlShortener\Middleware;

use Predis\ClientInterface;

/**
 * Token Bucket rate limiter using Redis.
 *
 * Each IP has a token bucket key. Tokens refill at a fixed rate.
 * If the bucket is empty, the request is rate-limited.
 */
final class RateLimitMiddleware
{
    private const string RATE_LIMIT_PREFIX = 'rate_limit:';
    private const int DEFAULT_MAX_TOKENS = 100;
    private const int DEFAULT_REFILL_RATE = 10; // tokens per second
    private const int DEFAULT_REFILL_INTERVAL = 1; // seconds

    private int $maxTokens;
    private int $refillRate;
    private int $refillInterval;

    public function __construct(
        private readonly ClientInterface $redis,
        ?int $maxTokens = null,
        ?int $refillRate = null,
        ?int $refillInterval = null
    ) {
        $this->maxTokens = $maxTokens ?? self::DEFAULT_MAX_TOKENS;
        $this->refillRate = $refillRate ?? self::DEFAULT_REFILL_RATE;
        $this->refillInterval = $refillInterval ?? self::DEFAULT_REFILL_INTERVAL;
    }

    public function isAllowed(string $ipAddress): bool
    {
        $key = self::RATE_LIMIT_PREFIX . $ipAddress;
        $now = time();

        $bucket = $this->redis->get($key);
        if ($bucket === null) {
            $this->redis->setex($key, 60, json_encode([
                'tokens' => $this->maxTokens - 1,
                'last_refill' => $now,
            ]));
            return true;
        }

        $data = json_decode($bucket, true);
        $tokens = $data['tokens'];
        $lastRefill = $data['last_refill'];

        $elapsed = $now - $lastRefill;
        $refillAmount = (int) ($elapsed / $this->refillInterval) * $this->refillRate;
        $tokens = min($this->maxTokens, $tokens + $refillAmount);

        if ($tokens <= 0) {
            return false;
        }

        $this->redis->setex($key, 60, json_encode([
            'tokens' => $tokens - 1,
            'last_refill' => $now,
        ]));

        return true;
    }

    public function getRemainingTokens(string $ipAddress): int
    {
        $key = self::RATE_LIMIT_PREFIX . $ipAddress;
        $bucket = $this->redis->get($key);
        if ($bucket === null) {
            return $this->maxTokens;
        }

        $data = json_decode($bucket, true);
        $tokens = $data['tokens'];
        $lastRefill = $data['last_refill'];

        $elapsed = time() - $lastRefill;
        $refillAmount = (int) ($elapsed / $this->refillInterval) * $this->refillRate;
        $tokens = min($this->maxTokens, $tokens + $refillAmount);

        return max(0, (int) $tokens);
    }
}
