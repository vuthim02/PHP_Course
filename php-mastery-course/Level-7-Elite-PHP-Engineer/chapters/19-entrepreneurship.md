# Chapter 19: Entrepreneurship

## Learning Objectives

- Start a PHP-based product company
- Validate product ideas
- Build and ship products
- Scale revenue and team

---

## 19.1 Building a Product

```php
<?php
class ProductIdea
{
    public function __construct(
        private string $name,
        private string $problem,
        private string $solution,
        private string $targetMarket,
    ) {}

    public function validate(): array
    {
        return [
            'problem_interviews' => 20,
            'landing_page_clicks' => 500,
            'signup_conversion' => 0.05,
            'willing_to_pay' => 0.30,
        ];
    }

    public function businessModel(): array
    {
        return [
            'pricing' => [
                'starter' => 29,
                'professional' => 79,
                'enterprise' => 299,
            ],
            'metrics' => [
                'target_mrr' => 50000,
                'target_ARR' => 600000,
                'estimated_cac' => 500,
                'estimated_ltv' => 3000,
            ],
            'channels' => [
                'Content marketing (blog, tutorials)',
                'Open source community',
                'PHP conferences and meetups',
                'Developer tool directories',
            ],
        ];
    }
}

class SaaSBusiness
{
    private array $metrics = [];

    public function __construct(
        private string $name,
        private float $monthlyPrice,
    ) {}

    public function trackMetric(string $name, float $value): void
    {
        $this->metrics[$name] = $value;
    }

    public function calculateMRR(): float
    {
        return $this->metrics['customers'] * $this->monthlyPrice;
    }

    public function calculateChurn(): float
    {
        $lost = $this->metrics['churned_last_month'] ?? 0;
        $total = $this->metrics['customers_start_of_month'] ?? 1;
        return $lost / $total;
    }

    public function calculateLTV(): float
    {
        $churn = $this->calculateChurn();
        $avgMonths = $churn > 0 ? 1 / $churn : 12;
        return $this->monthlyPrice * $avgMonths;
    }

    public function growthStage(): string
    {
        $mrr = $this->calculateMRR();
        return match(true) {
            $mrr < 1000 => 'Validation',
            $mrr < 10000 => 'Survival',
            $mrr < 50000 => 'Growth',
            $mrr < 100000 => 'Scale',
            default => 'Maturity',
        };
    }

    public function dashboard(): array
    {
        return [
            'MRR' => $this->calculateMRR(),
            'ARR' => $this->calculateMRR() * 12,
            'Churn Rate' => round($this->calculateChurn() * 100, 2) . '%',
            'LTV' => $this->calculateLTV(),
            'Growth Stage' => $this->growthStage(),
            'Runway (months)' => $this->metrics['cash'] / $this->metrics['monthly_burn'],
        ];
    }
}
```

---

## 19.2 Exercises

1. Identify 3 problems in PHP ecosystem worth solving
2. Validate one idea with customer interviews
3. Build an MVP and launch it
4. Acquire your first 10 paying customers

---

## Further Reading

- **Book:** "The Lean Startup" by Eric Ries
- **Book:** "Zero to Sold" by Arvid Kahl
- **Podcast:** "Software Social" by Justin Jackson
