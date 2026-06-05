# Chapter 9: Cost Optimization

## Learning Objectives

- Optimize cloud infrastructure costs
- Reduce database spending
- Implement cache economics
- Build cost-aware architectures

---

## 9.1 Cost Analysis

```php
<?php
class CostOptimizer
{
    private array $services = [];
    private array $costs = [];

    public function addService(string $name, float $monthlyCost): void
    {
        $this->services[$name] = ['cost' => $monthlyCost];
    }

    public function addCost(string $service, string $category, float $amount): void
    {
        $this->costs[$service][$category] = ($this->costs[$service][$category] ?? 0) + $amount;
    }

    public function getCostBreakdown(): array
    {
        $breakdown = [];
        foreach ($this->services as $name => $service) {
            $breakdown[$name] = [
                'monthly' => $service['cost'],
                'annual' => $service['cost'] * 12,
                'components' => $this->costs[$name] ?? [],
            ];
        }
        return $breakdown;
    }

    public function suggestOptimizations(): array
    {
        $suggestions = [];

        // Database cost optimization
        if (($this->costs['database']['storage'] ?? 0) > 100) {
            $suggestions[] = 'Consider archival of old data to S3 Glacier';
        }

        // Cache optimization
        if (($this->costs['cache']['redis'] ?? 0) > 200) {
            $suggestions[] = 'Right-size Redis cluster and use intelligent TTL';
        }

        // Compute optimization
        if (($this->costs['compute']['ec2'] ?? 0) > 500) {
            $suggestions[] = 'Use spot instances for non-critical workloads';
            $suggestions[] = 'Consider Graviton ARM instances for better perf/$';
        }

        return $suggestions;
    }
}

// Cache economics
class CacheEconomics
{
    public function __construct(
        private float $cacheCostPerGB = 0.125,  // Redis cost per GB/hour
        private float $databaseCostPerQuery = 0.00001,  // Per query cost
        private float $cacheHitRate = 0.85,  // 85% cache hit rate
    ) {}

    public function calculateSavings(int $dailyQueries, int $cacheSizeGB): array
    {
        $dailyDbQueries = $dailyQueries;
        $dailyCacheHits = $dailyDbQueries * $this->cacheHitRate;
        $dailyCacheMisses = $dailyDbQueries - $dailyCacheHits;

        $dailyDbCost = $dailyDbQueries * $this->databaseCostPerQuery;
        $dailyCacheCost = $cacheSizeGB * $this->cacheCostPerGB * 24;
        $dailySavings = ($dailyCacheHits * $this->databaseCostPerQuery) - $dailyCacheCost;

        return [
            'daily_db_queries' => $dailyDbQueries,
            'daily_cache_hits' => (int)$dailyCacheHits,
            'daily_cache_misses' => (int)$dailyCacheMisses,
            'daily_db_cost' => round($dailyDbCost, 2),
            'daily_cache_cost' => round($dailyCacheCost, 2),
            'daily_savings' => round($dailySavings, 2),
            'monthly_savings' => round($dailySavings * 30, 2),
            'annual_savings' => round($dailySavings * 365, 2),
        ];
    }
}
```

---

## 9.2 Exercises

1. Analyze cloud costs for a production environment
2. Identify top 5 cost optimization opportunities
3. Implement right-sizing for EC2/RDS instances
4. Build a cost dashboard with projected savings

---

## Further Reading

- **Doc:** [AWS Cost Optimization](https://aws.amazon.com/architecture/cost-optimization/)
- **Doc:** [Google Cloud Cost Optimization](https://cloud.google.com/architecture/framework/cost-optimization)
