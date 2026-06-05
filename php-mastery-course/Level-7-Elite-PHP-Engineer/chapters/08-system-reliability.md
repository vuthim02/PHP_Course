# Chapter 8: System Reliability

## Learning Objectives

- Define SLOs and SLIs
- Implement error budgets
- Build incident response processes
- Create runbooks

---

## 8.1 SLOs and Error Budgets

```php
<?php
class SLO
{
    private array $metrics = [];

    public function __construct(
        private readonly string $name,
        private readonly float $target,  // 99.9 = 99.9%
        private readonly string $window,  // "30d"
    ) {}

    public function recordSuccess(): void
    {
        $this->metrics[] = ['time' => microtime(true), 'success' => true];
    }

    public function recordFailure(): void
    {
        $this->metrics[] = ['time' => microtime(true), 'success' => false];
    }

    public function getAvailability(): float
    {
        $this->pruneOldMetrics();
        
        $total = count($this->metrics);
        if ($total === 0) return 1.0;

        $successes = count(array_filter(
            $this->metrics,
            fn($m) => $m['success']
        ));

        return $successes / $total;
    }

    public function getErrorBudget(): float
    {
        $availability = $this->getAvailability();
        $budget = $this->target - $availability;
        return max(0, $budget);
    }

    public function isErrorBudgetExhausted(): bool
    {
        return $this->getErrorBudget() <= 0;
    }

    private function pruneOldMetrics(): void
    {
        $cutoff = strtotime("-{$this->window}");
        $this->metrics = array_filter(
            $this->metrics,
            fn($m) => $m['time'] >= $cutoff
        );
    }
}

// Incident response
class Incident
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $startedAt,
        public string $severity,  // SEV1, SEV2, SEV3
        public string $status,    // detecting, mitigating, resolved, monitoring
        public string $description,
        public ?string $rootCause = null,
    ) {}

    public function acknowledge(string $engineer): void
    {
        $this->status = 'mitigating';
        $this->log("Acknowledged by {$engineer}");
    }

    public function resolve(string $fix): void
    {
        $this->status = 'resolved';
        $this->rootCause = $fix;
        $this->log("Resolved: {$fix}");
    }

    public function createPostmortem(): Postmortem
    {
        return new Postmortem(
            incidentId: $this->id,
            summary: $this->description,
            rootCause: $this->rootCause,
            actionItems: [],
            date: new \DateTimeImmutable(),
        );
    }

    private function log(string $message): void
    {
        // Log to incident management system
    }
}
```

---

## 8.2 Exercises

1. Define SLOs for an API service (availability, latency, throughput)
2. Implement error budget tracking
3. Create an incident response runbook
4. Write a postmortem for a past production incident

---

## Further Reading

- **Book:** "Site Reliability Engineering" by Google
- **Doc:** [SLI/SLO Guide](https://sre.google/sre-book/service-level-objectives/)
