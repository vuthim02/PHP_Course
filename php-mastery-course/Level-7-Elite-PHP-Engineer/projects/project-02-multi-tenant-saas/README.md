# Project 2: Multi-Tenant SaaS Platform

A production-grade multi-tenant SaaS core with database-per-tenant isolation, subscription management, and admin dashboard.

## Elite PHP Engineer Concepts

- **Multi-Tenancy Architecture**: Database-per-tenant with dynamic connection pooling
- **System Design**: Tenant resolution, provisioning pipelines, feature flags
- **Billing & Metering**: Usage-based billing with metering aggregation
- **Security Architecture**: Tenant isolation at database, application, and cache layers
- **Migration Strategy**: Tenant-aware migrations for zero-downtime schema updates
- **Domain-Driven Design**: Bounded contexts for Tenant, Billing, FeatureFlags

## Architecture

```
┌─────────────┐     ┌───────────────────┐     ┌──────────────────┐
│  Request     │────▶│  Domain           │────▶│  Tenant Context   │
│  (Host:      │     │  Resolution       │     │  Resolver         │
│   clientX.io)│     │  Middleware       │     │                   │
└─────────────┘     └───────────────────┘     └────────┬─────────┘
                                                        │
              ┌─────────────────────────────────────────┼─────────┐
              │                                         │         │
              ▼                                         ▼         │
   ┌──────────────────┐                    ┌────────────────────┐  │
   │  Central DB       │                    │  Tenant DB Pool    │  │
   │  (tenants, plans, │                    │  (one DB per       │  │
   │   feature_flags)  │                    │   tenant, dynamic) │  │
   └──────────────────┘                    └────────────────────┘  │
              │                                         │         │
              ▼                                         ▼         │
   ┌──────────────────┐                    ┌────────────────────┐  │
   │  Admin Dashboard  │                    │  Billing Metering   │  │
   │  (global)         │                    │  Usage Aggregation  │  │
   └──────────────────┘                    └────────────────────┘  │
                                                        │         │
              ┌─────────────────────────────────────────┼─────────┘
              │                                         │
              ▼                                         ▼
   ┌──────────────────┐                    ┌────────────────────┐
   │  Feature Flags    │                    │  Tenant            │
   │  (per-tenant      │                    │  Provisioner       │
   │   overrides)      │                    │  (new tenant flow) │
   └──────────────────┘                    └────────────────────┘
```

### Tenant Isolation Strategy

- **Database-per-tenant**: Each tenant gets an isolated MySQL database
- **Connection pooling**: Dynamic PDO connections cached in memory
- **Schema separation**: Central `tenants` table + per-tenant databases
- **Domain resolution**: Map `Host` header to tenant ID via Redis cache

### Subscription & Billing

- **Plan based**: Monthly/annual plans with feature limits
- **Usage metering**: Track API calls, storage, users per tenant
- **Invoicing**: Auto-generate invoices on billing cycles
- **Metering aggregation**: Hourly/daily aggregation for billing

## Performance Considerations

- **Connection pooling**: Reuse tenant PDO connections via static pool
- **Redis caching**: Tenant resolution cached (sub-ms lookups)
- **Lazy connections**: Tenant DB connections created only on first request
- **Read replicas**: Each tenant can have replica connections
- **Async metering**: Usage events queued for batch processing

## Setup

```bash
composer install
cp config/config.example.php config/config.php
# Edit config.php with your central DB credentials
php -S localhost:8080 -t public
```

## Migrations

```bash
# Run central migrations (platform-wide schema)
php migrations/migrate.php central

# Run tenant migrations (for a specific tenant's database)
php migrations/migrate.php tenant --tenant=tenant_id
```

## Extending

- Add `WebhookService` for tenant-specific webhook delivery
- Implement `AuditLog` across all tenant operations
- Add `BackupService` for per-tenant database backups
- Implement `TenantAnalytics` aggregation pipeline

## Real-World Use Cases

- White-label SaaS platforms (e.g., Shopify, Salesforce)
- Enterprise multi-org B2B applications
- Reseller/agency management platforms
- Platform-as-a-Service offering
