<?php

declare(strict_types=1);

namespace UrlShortener\Repository;

final class AnalyticsRepository
{
    public function __construct(
        private readonly \PDO $primary
    ) {}

    public function getClickCount(string $shortCode): int
    {
        $stmt = $this->primary->prepare(
            'SELECT COUNT(*) as count FROM click_analytics WHERE short_code = :short_code'
        );
        $stmt->execute(['short_code' => $shortCode]);
        return (int) $stmt->fetchColumn();
    }

    public function getClicksInRange(
        string $shortCode,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to
    ): array {
        $stmt = $this->primary->prepare(
            'SELECT DATE(clicked_at) as date, COUNT(*) as count
             FROM click_analytics
             WHERE short_code = :short_code
               AND clicked_at >= :from
               AND clicked_at <= :to
             GROUP BY DATE(clicked_at)
             ORDER BY date ASC'
        );
        $stmt->execute([
            'short_code' => $shortCode,
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTopReferrers(string $shortCode, int $limit = 10): array
    {
        $stmt = $this->primary->prepare(
            'SELECT referer, COUNT(*) as count
             FROM click_analytics
             WHERE short_code = :short_code
             GROUP BY referer
             ORDER BY count DESC
             LIMIT :limit'
        );
        $stmt->bindValue('short_code', $shortCode);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function bulkInsert(array $clicks): int
    {
        if (empty($clicks)) {
            return 0;
        }

        $this->primary->beginTransaction();

        try {
            $stmt = $this->primary->prepare(
                'INSERT INTO click_analytics (short_code, ip_address, user_agent, referer, clicked_at)
                 VALUES (:short_code, :ip_address, :user_agent, :referer, :clicked_at)'
            );

            $inserted = 0;
            foreach ($clicks as $click) {
                $stmt->execute([
                    'short_code' => $click['short_code'],
                    'ip_address' => $click['ip_address'] ?? '',
                    'user_agent' => $click['user_agent'] ?? '',
                    'referer' => $click['referer'] ?? '',
                    'clicked_at' => $click['clicked_at'] ?? date('Y-m-d H:i:s'),
                ]);
                $inserted++;
            }

            $this->primary->commit();
            return $inserted;
        } catch (\Throwable $e) {
            $this->primary->rollBack();
            throw $e;
        }
    }
}
