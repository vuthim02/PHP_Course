<?php

declare(strict_types=1);

namespace UrlShortener\Service;

use UrlShortener\Cache\CacheInterface;
use UrlShortener\Queue\AnalyticsJob;
use UrlShortener\Queue\RedisQueue;
use UrlShortener\Repository\AnalyticsRepository;

final class AnalyticsService
{
    private const string CACHE_KEY_CLICKS = 'analytics:clicks:';
    private const int CACHE_TTL = 3600;

    public function __construct(
        private readonly RedisQueue $queue,
        private readonly AnalyticsRepository $repository,
        private readonly CacheInterface $cache
    ) {}

    public function recordClick(
        string $shortCode,
        string $ipAddress,
        string $userAgent,
        string $referer
    ): void {
        $job = new AnalyticsJob(
            $shortCode,
            $ipAddress,
            $userAgent,
            $referer,
            time()
        );

        $this->queue->push($job);

        $this->cache->increment(self::CACHE_KEY_CLICKS . $shortCode);
    }

    public function getClickCount(string $shortCode): int
    {
        $cached = $this->cache->get(self::CACHE_KEY_CLICKS . $shortCode);
        if ($cached !== null) {
            return (int) $cached;
        }

        $count = $this->repository->getClickCount($shortCode);
        $this->cache->set(self::CACHE_KEY_CLICKS . $shortCode, $count, self::CACHE_TTL);

        return $count;
    }

    public function getClicksOverTime(
        string $shortCode,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to
    ): array {
        return $this->repository->getClicksInRange($shortCode, $from, $to);
    }

    public function getTopReferrers(string $shortCode, int $limit = 10): array
    {
        return $this->repository->getTopReferrers($shortCode, $limit);
    }
}
