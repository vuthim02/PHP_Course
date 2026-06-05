<?php

declare(strict_types=1);

namespace MultiTenant\Core\Billing;

final class PlanService
{
    /** @var array<string, array> */
    private static array $plans = [];

    public function __construct(
        private readonly \PDO $centralPdo
    ) {}

    public function getPlan(string $planSlug): ?array
    {
        $this->loadPlans();

        return self::$plans[$planSlug] ?? null;
    }

    public function getAllPlans(): array
    {
        $this->loadPlans();
        return array_values(self::$plans);
    }

    public function createPlan(string $name, string $slug, float $price, array $features, string $billingInterval = 'monthly'): array
    {
        $stmt = $this->centralPdo->prepare(
            'INSERT INTO plans (name, slug, price, features, billing_interval, created_at)
             VALUES (:name, :slug, :price, :features, :billing_interval, NOW())'
        );
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'price' => $price,
            'features' => json_encode($features),
            'billing_interval' => $billingInterval,
        ]);

        // Clear cached plans
        self::$plans = [];

        return $this->getPlan($slug);
    }

    /**
     * Check if a tenant's current usage exceeds their plan limits.
     */
    public function checkQuota(string $tenantId, string $metric, int $currentUsage): bool
    {
        $stmt = $this->centralPdo->prepare(
            'SELECT t.plan, p.features
             FROM tenants t
             JOIN plans p ON t.plan = p.slug
             WHERE t.id = :tenant_id
             LIMIT 1'
        );
        $stmt->execute(['tenant_id' => $tenantId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        $features = json_decode($row['features'], true);
        $limit = $features['limits'][$metric] ?? PHP_INT_MAX;

        return $currentUsage < $limit;
    }

    private function loadPlans(): void
    {
        if (!empty(self::$plans)) {
            return;
        }

        $stmt = $this->centralPdo->query(
            'SELECT slug, name, price, features, billing_interval FROM plans WHERE active = 1'
        );

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['features'] = json_decode($row['features'], true);
            self::$plans[$row['slug']] = $row;
        }
    }
}
