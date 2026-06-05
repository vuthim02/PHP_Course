<?php

declare(strict_types=1);

namespace UrlShortener\Queue;

use Predis\ClientInterface;

final class RedisQueue
{
    private const string QUEUE_KEY = 'queue:analytics';
    private const string DELAYED_KEY = 'queue:delayed';
    private const string DEAD_LETTER_KEY = 'queue:dead_letter';

    public function __construct(
        private readonly ClientInterface $redis
    ) {}

    public function push(JobInterface $job, int $delay = 0): void
    {
        $payload = json_encode([
            'class' => $job::class,
            'payload' => $job->getPayload(),
            'retries' => 0,
            'maxRetries' => $job->getMaxRetries(),
            'createdAt' => time(),
        ]);

        if ($delay > 0) {
            $this->redis->zadd(self::DELAYED_KEY, [
                $payload => time() + $delay,
            ]);
        } else {
            $this->redis->rpush(self::QUEUE_KEY, [$payload]);
        }
    }

    public function pop(): ?JobInterface
    {
        $this->migrateDelayedJobs();

        $raw = $this->redis->lpop(self::QUEUE_KEY);
        if ($raw === null) {
            return null;
        }

        $data = json_decode($raw, true);
        if (!isset($data['class']) || !class_exists($data['class'])) {
            return null;
        }

        $class = $data['class'];
        $job = new $class(...$data['payload']);
        return $job;
    }

    public function failed(string $payload): void
    {
        $this->redis->rpush(self::DEAD_LETTER_KEY, [$payload]);
    }

    public function length(): int
    {
        return $this->redis->llen(self::QUEUE_KEY);
    }

    public function deadLetterCount(): int
    {
        return $this->redis->llen(self::DEAD_LETTER_KEY);
    }

    private function migrateDelayedJobs(): void
    {
        $now = time();
        $jobs = $this->redis->zrangebyscore(self::DELAYED_KEY, 0, $now);

        foreach ($jobs as $job) {
            $this->redis->rpush(self::QUEUE_KEY, [$job]);
            $this->redis->zrem(self::DELAYED_KEY, [$job]);
        }
    }
}
