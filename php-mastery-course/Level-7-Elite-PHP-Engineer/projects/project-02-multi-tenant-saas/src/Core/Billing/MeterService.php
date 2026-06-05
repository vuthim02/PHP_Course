<?php

declare(strict_types=1);

namespace MultiTenant\Core\Billing;

/**
 * Usage metering service for consumption-based billing.
 * Records usage events and provides aggregation for invoicing.
 */
final class MeterService
{
    private const string METER_TABLE = 'usage_metering';
    private const string CACHE_KEY_PREFIX = 'meter:';

    public function __construct(
        private readonly \PDO $centralPdo
    ) {}

    /**
     * Record a usage event for a tenant.
     */
    public function record(string $tenantId, string $metric, int $amount = 1, ?string $description = null): void
    {
        $stmt = $this->centralPdo->prepare(
            "INSERT INTO {self::METER_TABLE} (tenant_id, metric, amount, description, recorded_at)
             VALUES (:tenant_id, :metric, :amount, :description, NOW())"
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'metric' => $metric,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    /**
     * Get aggregated usage for a tenant within a time range.
     */
    public function getUsage(string $tenantId, string $metric, \DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        $stmt = $this->centralPdo->prepare(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM {self::METER_TABLE}
             WHERE tenant_id = :tenant_id
               AND metric = :metric
               AND recorded_at >= :from
               AND recorded_at <= :to"
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'metric' => $metric,
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s'),
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all metered usage for a tenant in the current billing period.
     */
    public function getCurrentPeriodUsage(string $tenantId, string $planSlug): array
    {
        $billingStart = $this->getBillingPeriodStart($tenantId, $planSlug);

        $stmt = $this->centralPdo->prepare(
            "SELECT metric, SUM(amount) as total
             FROM {self::METER_TABLE}
             WHERE tenant_id = :tenant_id
               AND recorded_at >= :billing_start
             GROUP BY metric"
        );
        $stmt->execute([
            'tenant_id' => $tenantId,
            'billing_start' => $billingStart->format('Y-m-d H:i:s'),
        ]);

        $usage = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $usage[$row['metric']] = (int) $row['total'];
        }

        return $usage;
    }

    private function getBillingPeriodStart(string $tenantId, string $planSlug): \DateTimeImmutable
    {
        $stmt = $this->centralPdo->prepare(
            'SELECT t.created_at, p.billing_interval
             FROM tenants t
             JOIN plans p ON t.plan = p.slug
             WHERE t.id = :tenant_id
             LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $createdAt = new \DateTimeImmutable($row['created_at']);
        $interval = $row['billing_interval'];

        $now = new \DateTimeImmutable();

        if ($interval === 'monthly') {
            $months = (int) $createdAt->diff($now)->m + ((int) $createdAt->diff($now)->y * 12);
            return $createdAt->modify("+{$months} months");
        }

        $years = (int) $createdAt->diff($now)->y;
        return $createdAt->modify("+{$years} years");
    }
}
