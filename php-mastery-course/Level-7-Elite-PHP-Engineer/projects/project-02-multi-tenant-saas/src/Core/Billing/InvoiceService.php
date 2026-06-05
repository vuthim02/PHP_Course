<?php

declare(strict_types=1);

namespace MultiTenant\Core\Billing;

final class InvoiceService
{
    public function __construct(
        private readonly \PDO $centralPdo,
        private readonly MeterService $meterService,
        private readonly PlanService $planService
    ) {}

    public function generateInvoice(string $tenantId): array
    {
        $stmt = $this->centralPdo->prepare(
            'SELECT id, name, plan, created_at FROM tenants WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $tenantId]);
        $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$tenant) {
            throw new \InvalidArgumentException("Tenant not found: {$tenantId}");
        }

        $plan = $this->planService->getPlan($tenant['plan']);
        if (!$plan) {
            throw new \RuntimeException("Plan not found: {$tenant['plan']}");
        }

        $usage = $this->meterService->getCurrentPeriodUsage($tenantId, $plan['slug']);
        $overageCost = $this->calculateOverage($usage, $plan['features']['limits'] ?? []);

        $total = $plan['price'] + $overageCost;

        $invoiceId = \Ramsey\Uuid\Uuid::uuid7()->toString();
        $stmt = $this->centralPdo->prepare(
            'INSERT INTO invoices (id, tenant_id, plan, base_amount, overage_amount, total_amount, status, generated_at, due_at)
             VALUES (:id, :tenant_id, :plan, :base_amount, :overage_amount, :total_amount, :status, NOW(), :due_at)'
        );
        $stmt->execute([
            'id' => $invoiceId,
            'tenant_id' => $tenantId,
            'plan' => $plan['name'],
            'base_amount' => $plan['price'],
            'overage_amount' => $overageCost,
            'total_amount' => $total,
            'status' => 'pending',
            'due_at' => (new \DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s'),
        ]);

        return [
            'id' => $invoiceId,
            'tenant_id' => $tenantId,
            'plan' => $plan['name'],
            'base_amount' => $plan['price'],
            'overage_amount' => $overageCost,
            'total_amount' => $total,
            'usage' => $usage,
        ];
    }

    public function getInvoices(string $tenantId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->centralPdo->prepare(
            'SELECT id, plan, base_amount, overage_amount, total_amount, status, generated_at, due_at, paid_at
             FROM invoices WHERE tenant_id = :tenant_id
             ORDER BY generated_at DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('tenant_id', $tenantId);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markAsPaid(string $invoiceId): void
    {
        $stmt = $this->centralPdo->prepare(
            'UPDATE invoices SET status = :status, paid_at = NOW() WHERE id = :id'
        );
        $stmt->execute(['status' => 'paid', 'id' => $invoiceId]);
    }

    private function calculateOverage(array $usage, array $limits): float
    {
        $overage = 0.0;

        // Example: $0.10 per 1000 API calls over limit
        if (isset($usage['api_calls'], $limits['api_calls'])) {
            $overageCalls = max(0, $usage['api_calls'] - $limits['api_calls']);
            $overage += ($overageCalls / 1000) * 0.10;
        }

        // Example: $0.01 per MB storage over limit
        if (isset($usage['storage_mb'], $limits['storage_mb'])) {
            $overageStorage = max(0, $usage['storage_mb'] - $limits['storage_mb']);
            $overage += $overageStorage * 0.01;
        }

        return round($overage, 2);
    }
}
