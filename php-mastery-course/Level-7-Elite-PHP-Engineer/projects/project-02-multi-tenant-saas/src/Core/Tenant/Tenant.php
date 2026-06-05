<?php

declare(strict_types=1);

namespace MultiTenant\Core\Tenant;

final class Tenant
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $domain,
        public readonly string $databaseName,
        public readonly string $plan,
        public readonly bool $active,
        public readonly array $settings = [],
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $trialEndsAt = null
    ) {}

    public function isTrial(): bool
    {
        if ($this->trialEndsAt === null) {
            return false;
        }
        return new \DateTimeImmutable() < $this->trialEndsAt;
    }

    public function isOnPlan(string ...$plans): bool
    {
        return in_array($this->plan, $plans, true);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }
}
