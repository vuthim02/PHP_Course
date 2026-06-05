# Chapter 22: Project: Enterprise CRM

## Project Overview

Build a complete enterprise Customer Relationship Management system with multi-tenancy, workflows, reporting, and integrations.

---

## 22.1 Requirements

### Features
- Multi-tenant (each company isolated)
- Contact management
- Deal pipeline (sales stages)
- Activity tracking (calls, emails, meetings)
- Email integration (Gmail, Outlook)
- Calendar sync
- Reporting and dashboards
- Workflow automation
- API for integrations
- RBAC with custom roles

### Technical Stack
- Laravel 11
- MySQL/TimescaleDB
- Redis (cache, queues, sessions)
- Elasticsearch (search)
- WebSockets (real-time)
- Docker/Kubernetes
- GitHub Actions CI/CD

---

## 22.2 Architecture

```
┌──────────────────────────────────────────────────────┐
│                    API Gateway                         │
├────────────┬──────────┬──────────┬────────────────────┤
│  Contacts  │  Deals   │   CRM    │ Communications     │
│  Service   │  Service │  Search  │   Service           │
├────────────┴──────────┴──────────┴────────────────────┤
│                    Message Bus (RabbitMQ)              │
├────────────┬──────────┬──────────┬────────────────────┤
│  Contacts  │  Deals   │  Email   │ Notifications      │
│   DB       │   DB     │ Service  │   Service          │
└────────────┴──────────┴──────────┴────────────────────┘
```

---

## 22.3 Core Entities

```php
<?php
namespace App\Domain\CRM;

class Contact
{
    public function __construct(
        public readonly string $id,
        public readonly string $tenantId,
        public string $name,
        public Email $email,
        public ?string $phone,
        public ?string $company,
        public string $status,  // lead, prospect, customer, churned
        public array $customFields,
        public \DateTimeImmutable $createdAt,
    ) {}
}

class Deal
{
    public function __construct(
        public readonly string $id,
        public readonly string $tenantId,
        public string $name,
        public float $value,
        public string $stage,  // prospecting, qualification, proposal, negotiation, closed_won, closed_lost
        public string $contactId,
        public ?\DateTimeImmutable $expectedCloseDate,
    ) {}

    public function moveToStage(string $stage): void
    {
        if (!$this->canTransitionTo($stage)) {
            throw new WorkflowException("Cannot move from {$this->stage} to {$stage}");
        }
        $this->stage = $stage;
    }

    private function canTransitionTo(string $target): bool
    {
        $transitions = [
            'prospecting' => ['qualification'],
            'qualification' => ['proposal', 'closed_lost'],
            'proposal' => ['negotiation', 'closed_lost'],
            'negotiation' => ['closed_won', 'closed_lost'],
            'closed_won' => [],
            'closed_lost' => ['prospecting'],
        ];

        return in_array($target, $transitions[$this->stage] ?? []);
    }
}

// Workflow automation
class WorkflowEngine
{
    private array $triggers = [];
    private array $actions = [];

    public function when(string $event, callable $condition): self
    {
        $this->triggers[$event][] = $condition;
        return $this;
    }

    public function then(callable $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function execute(string $event, mixed $data): void
    {
        $conditions = $this->triggers[$event] ?? [];
        
        foreach ($conditions as $condition) {
            if (!$condition($data)) {
                return;
            }
        }

        foreach ($this->actions as $action) {
            $action($data);
        }
    }
}

// Example automation
$workflow = new WorkflowEngine();
$workflow
    ->when('deal.created', fn(Deal $deal) => $deal->value > 10000)
    ->when('deal.created', fn(Deal $deal) => $deal->stage === 'prospecting')
    ->then(fn(Deal $deal) => Notifications::notifySalesManager($deal))
    ->then(fn(Deal $deal) => Slack::sendMessage("#high-value-deals", "New high-value deal: {$deal->name}"));
```

---

## 22.4 Deliverables

1. Multi-tenant CRM with contact and deal management
2. Deal pipeline with drag-and-drop kanban
3. Calendar and email integration
4. Reporting dashboard with charts
5. Workflow automation engine
6. REST API and webhooks
7. Elasticsearch-powered search
8. Real-time notifications
9. Comprehensive test suite
10. CI/CD and infrastructure as code

---

## Further Reading

- **Doc:** [Laravel](https://laravel.com/docs)
- **Doc:** [Elasticsearch PHP](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html)
