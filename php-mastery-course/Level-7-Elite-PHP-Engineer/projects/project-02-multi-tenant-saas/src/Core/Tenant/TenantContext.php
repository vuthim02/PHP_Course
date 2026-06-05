<?php

declare(strict_types=1);

namespace MultiTenant\Core\Tenant;

/**
 * Holds the current tenant context for the request lifecycle.
 * Accessible via static scope for service layer consumption.
 */
final class TenantContext
{
    private static ?TenantContext $instance = null;

    private ?Tenant $tenant = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function getId(): ?string
    {
        return $this->tenant?->id;
    }

    public function getDatabaseName(): ?string
    {
        return $this->tenant?->databaseName;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }
}
