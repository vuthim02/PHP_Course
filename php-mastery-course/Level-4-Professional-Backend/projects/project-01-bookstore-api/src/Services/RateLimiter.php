<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class RateLimiter
{
    private int $maxRequests;
    private int $windowSeconds;

    public function __construct()
    {
        $this->maxRequests = (int) ($_ENV['RATE_LIMIT_PER_MINUTE'] ?? 100);
        $this->windowSeconds = 60;
    }

    public function check(string $key): array
    {
        $db = Database::getInstance();
        $now = time();
        $windowStart = $now - $this->windowSeconds;

        $db->query(
            "DELETE FROM rate_limits WHERE `key` = ? AND created_at < ?",
            [$key, $windowStart]
        );

        $existing = $db->fetch(
            "SELECT COUNT(*) as count, MIN(created_at) as window_start FROM rate_limits WHERE `key` = ? AND created_at >= ?",
            [$key, $windowStart]
        );

        $count = (int) ($existing['count'] ?? 0);

        if ($count >= $this->maxRequests) {
            $oldest = (int) ($existing['window_start'] ?? $now);
            $retryAfter = $oldest + $this->windowSeconds - $now;

            return [
                'allowed' => false,
                'limit' => $this->maxRequests,
                'remaining' => 0,
                'retry_after' => max($retryAfter, 1),
                'reset_at' => $oldest + $this->windowSeconds,
            ];
        }

        $db->insert('rate_limits', [
            'key' => $key,
            'created_at' => $now,
        ]);

        return [
            'allowed' => true,
            'limit' => $this->maxRequests,
            'remaining' => $this->maxRequests - $count - 1,
            'reset_at' => $now + $this->windowSeconds,
            'retry_after' => 0,
        ];
    }
}
