<?php

declare(strict_types=1);

namespace UrlShortener\Queue;

final class AnalyticsJob implements JobInterface
{
    private const int MAX_RETRIES = 3;

    public function __construct(
        private readonly string $shortCode,
        private readonly string $ipAddress,
        private readonly string $userAgent,
        private readonly string $referer,
        private readonly int $timestamp,
        private int $retries = 0
    ) {}

    public function handle(): void
    {
        $pdo = new \PDO(
            $_ENV['DB_DSN'] ?? 'mysql:host=127.0.0.1;dbname=url_shortener',
            $_ENV['DB_USER'] ?? 'root',
            $_ENV['DB_PASS'] ?? '',
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare(
            'INSERT INTO click_analytics (short_code, ip_address, user_agent, referer, clicked_at)
             VALUES (:short_code, :ip_address, :user_agent, :referer, :clicked_at)'
        );

        $stmt->execute([
            'short_code' => $this->shortCode,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
            'clicked_at' => date('Y-m-d H:i:s', $this->timestamp),
        ]);
    }

    public function getName(): string
    {
        return 'analytics';
    }

    public function getPayload(): array
    {
        return [
            'shortCode' => $this->shortCode,
            'ipAddress' => $this->ipAddress,
            'userAgent' => $this->userAgent,
            'referer' => $this->referer,
            'timestamp' => $this->timestamp,
        ];
    }

    public function getRetries(): int
    {
        return $this->retries;
    }

    public function getMaxRetries(): int
    {
        return self::MAX_RETRIES;
    }
}
