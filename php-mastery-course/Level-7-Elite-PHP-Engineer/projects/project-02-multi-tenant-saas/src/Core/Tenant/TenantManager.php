<?php

declare(strict_types=1);

namespace MultiTenant\Core\Tenant;

use Predis\ClientInterface;

/**
 * Manages tenant lifecycle: resolution, caching, and CRUD operations.
 * Tenants are cached in Redis for sub-millisecond lookup on every request.
 */
final class TenantManager
{
    private const string CACHE_KEY_PREFIX = 'tenant:domain:';
    private const string CACHE_KEY_ID = 'tenant:id:';
    private const int CACHE_TTL = 3600;

    public function __construct(
        private readonly \PDO $centralPdo,
        private readonly ClientInterface $redis
    ) {}

    public function resolveByDomain(string $domain): ?Tenant
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $domain;

        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return $this->hydrate(json_decode($cached, true));
        }

        $stmt = $this->centralPdo->prepare(
            'SELECT id, name, domain, database_name, plan, active, settings, created_at, trial_ends_at
             FROM tenants WHERE domain = :domain AND active = 1
             LIMIT 1'
        );
        $stmt->execute(['domain' => $domain]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $this->redis->setex($cacheKey, self::CACHE_TTL, json_encode($row));
        $this->redis->setex(self::CACHE_KEY_ID . $row['id'], self::CACHE_TTL, json_encode($row));

        return $this->hydrate($row);
    }

    public function resolveById(string $id): ?Tenant
    {
        $cacheKey = self::CACHE_KEY_ID . $id;

        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return $this->hydrate(json_decode($cached, true));
        }

        $stmt = $this->centralPdo->prepare(
            'SELECT id, name, domain, database_name, plan, active, settings, created_at, trial_ends_at
             FROM tenants WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $this->redis->setex($cacheKey, self::CACHE_TTL, json_encode($row));

        return $this->hydrate($row);
    }

    public function createTenant(string $name, string $domain, string $plan = 'free'): Tenant
    {
        $id = \Ramsey\Uuid\Uuid::uuid7()->toString();
        $databaseName = 'tenant_' . str_replace('-', '_', $id);

        $stmt = $this->centralPdo->prepare(
            'INSERT INTO tenants (id, name, domain, database_name, plan, active, settings, created_at)
             VALUES (:id, :name, :domain, :database_name, :plan, 1, :settings, NOW())'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'domain' => $domain,
            'database_name' => $databaseName,
            'plan' => $plan,
            'settings' => json_encode([]),
        ]);

        return new Tenant(
            id: $id,
            name: $name,
            domain: $domain,
            databaseName: $databaseName,
            plan: $plan,
            active: true,
            createdAt: new \DateTimeImmutable()
        );
    }

    public function invalidateCache(string $domain, string $id): void
    {
        $this->redis->del([self::CACHE_KEY_PREFIX . $domain, self::CACHE_KEY_ID . $id]);
    }

    public function getAllTenants(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->centralPdo->prepare(
            'SELECT id, name, domain, database_name, plan, active, created_at
             FROM tenants ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function hydrate(array $row): Tenant
    {
        return new Tenant(
            id: $row['id'],
            name: $row['name'],
            domain: $row['domain'],
            databaseName: $row['database_name'],
            plan: $row['plan'],
            active: (bool) $row['active'],
            settings: isset($row['settings']) ? json_decode($row['settings'], true) : [],
            createdAt: isset($row['created_at']) ? new \DateTimeImmutable($row['created_at']) : null,
            trialEndsAt: isset($row['trial_ends_at']) ? new \DateTimeImmutable($row['trial_ends_at']) : null
        );
    }
}
