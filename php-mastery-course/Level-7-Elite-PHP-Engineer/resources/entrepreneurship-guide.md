# Entrepreneurship & Open Source — Guide for Elite Engineers

## Building a PHP SaaS Product

### Validating an Idea

```php
// Step 1: Problem interview
// Talk to 20+ potential customers. Do NOT pitch your solution.
// Ask:
// - "Tell me about how you handle X today"
// - "What's the hardest part about X?"
// - "Have you tried to solve this? What happened?"

// Step 2: Solution validation
// Build a landing page describing the solution.
// Collect email sign-ups or pre-orders.

// Step 3: MVP ($50/month target)
// - Single feature
// - Stripe integration
// - 10 paying customers
// - Done in < 30 days

// Step 4: Growth
// If > 10 customers: add payment tier, iterate
// If < 5 customers: pivot or kill
```

### SaaS Architecture

```php
// Multi-tenant architecture (single database, row-level isolation)
class TenantScopedRepository
{
    public function __construct(
        private PDO $pdo,
        private int $tenantId,
    ) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM projects WHERE tenant_id = ?'
        );
        $stmt->execute([$this->tenantId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $data['tenant_id'] = $this->tenantId;
        // INSERT ...
    }
}

// Tenant resolution middleware
class TenantMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        $tenantId = $this->resolveTenant($request);

        if (!$tenantId) {
            return new JsonResponse(['error' => 'Invalid tenant'], 401);
        }

        // Bind tenant to container
        app()->instance(TenantContext::class, new TenantContext($tenantId));

        return $next($request);
    }

    private function resolveTenant(Request $request): ?int
    {
        // From subdomain: acme.myapp.com
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // Or from API key: X-Api-Key header
        $apiKey = $request->header('X-Api-Key');

        return Tenant::findByDomainOrKey($subdomain, $apiKey)?->id;
    }
}
```

### Subscription & Billing

```php
// Stripe integration with PHP
class SubscriptionService
{
    public function __construct(
        private \Stripe\StripeClient $stripe,
        private PDO $pdo,
    ) {}

    public function createSubscription(
        int $userId,
        string $priceId,
        string $paymentMethodId
    ): Subscription {
        $user = $this->findUser($userId);

        // Create or retrieve Stripe customer
        if (!$user->stripeCustomerId) {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'payment_method' => $paymentMethodId,
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);
            $this->updateUserStripeId($userId, $customer->id);
            $user->stripeCustomerId = $customer->id;
        }

        // Create subscription
        $subscription = $this->stripe->subscriptions->create([
            'customer' => $user->stripeCustomerId,
            'items' => [['price' => $priceId]],
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Store in local DB
        $this->storeSubscription($userId, $subscription);

        return Subscription::fromStripe($subscription);
    }

    public function handleWebhook(string $payload, string $sigHeader): void
    {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            $_ENV['STRIPE_WEBHOOK_SECRET']
        );

        match ($event->type) {
            'invoice.payment_succeeded' => $this->handlePaymentSuccess($event),
            'invoice.payment_failed' => $this->handlePaymentFailed($event),
            'customer.subscription.updated' => $this->handleSubscriptionUpdate($event),
            'customer.subscription.deleted' => $this->handleSubscriptionCancel($event),
            default => null,
        };
    }
}
```

### Usage-Based Pricing

```php
// Metered billing example
class UsageTracker
{
    public function __construct(private Redis $redis) {}

    public function track(int $tenantId, string $metric, int $amount = 1): void
    {
        $key = "usage:$tenantId:$metric:" . date('Y-m-d');
        $this->redis->incrby($key, $amount);
        $this->redis->expire($key, 86400 * 32);  // Keep 31 days
    }

    public function getUsage(int $tenantId, string $metric, string $period = 'month'): int
    {
        return match ($period) {
            'day' => (int) $this->redis->get("usage:$tenantId:$metric:" . date('Y-m-d')),
            'month' => $this->getMonthUsage($tenantId, $metric),
            'total' => $this->getTotalUsage($tenantId, $metric),
        };
    }

    private function getMonthUsage(int $tenantId, string $metric): int
    {
        $total = 0;
        $month = date('Y-m');
        $days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

        for ($day = 1; $day <= $days; $day++) {
            $dayStr = "$month-" . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
            $total += (int) $this->redis->get("usage:$tenantId:$metric:$dayStr");
        }

        return $total;
    }
}

// Price tiers
$pricing = [
    'starter' => ['api_calls' => 10000, 'price' => 29],
    'pro' => ['api_calls' => 100000, 'price' => 99],
    'enterprise' => ['api_calls' => -1, 'price' => 'Custom'],  // unlimited
];
```

## Open Source Strategy

### Why Build Open Source

```
Personal benefits:
- Recognition and credibility (elite engineer brand)
- Job offers (top companies recruit open source maintainers)
- Network (co-maintainers, contributors, users)
- Better code (review from world-class engineers)
- Portfolio (concrete proof of your skills)

Company benefits:
- Recruiting pipeline (contributors are pre-vetted)
- Community goodwill
- Standards influence
- Cost sharing (community bug fixes)
```

### Growing a PHP Package

```php
// 1. Solve a real problem — not "yet another" package
// Check Packagist: is the problem already solved well?

// 2. Great README
// - Clear description (one sentence)
// - Installation (one line)
// - Quick start (copy-paste example)
// - API reference
// - FAQ
// - Contributing guide

// 3. Quality signals
// - ✅ CI passing badge
// - ✅ Packagist downloads
// - ✅ PHPStan level max
// - ✅ 90%+ test coverage
// - ✅ Semantic versioning
// - ✅ CHANGELOG.md

// 4. Community building
// - GitHub Discussions
// - Twitter (#php, #laravel, #symfony)
// - PHP community Slack/Discord
// - Contribute to other projects (visibility)
// - Give talks about the problem domain
```

### Maintainer Burnout Prevention

```
- Set clear expectations (reply within 48h, releases monthly)
- Automate everything (CI, release workflow, stale bot)
- Say no (don't accept every feature request)
- Recruit co-maintainers (active contributors → commit bit)
- Take breaks (vacation mode on GitHub)
- Remember: It's OK to archive a project

Tools:
- Stale bot (close inactive issues)
- Release drafter (auto-generate changelog)
- Dependabot (auto-update dependencies)
- All contributors bot (recognition)
```

## Consulting as a PHP Expert

### Finding Clients

```
Channels:
1. Upwork / Toptal (start here, build reputation)
2. LinkedIn (publish technical content)
3. PHP conferences (speak → be seen)
4. Meetups (local PHP user groups)
5. Referrals (best source long-term)
6. Cold outreach (target specific companies with pain points)

Niche down:
- "I help Laravel companies scale past 10M requests/day"
- "I fix legacy PHP applications and modernize them"
- "I optimize PHP apps that crash under load"
```

### Engagement Structure

```
Discovery Phase (paid, 1-2 weeks):
- Audit codebase, infrastructure, team
- Deliver: Assessment report + recommendation

Execution Phase (monthly retainer):
- 20h/month minimum
- Weekly check-ins
- Code reviews, architecture, mentoring
- Deliver: Code, ADRs, runbooks

Escalation (on-call):
- Emergency rate (2x normal)
- Response time: 30min
- Only for critical issues
```

### Contract Essentials

```
- Scope of work (exactly what you will/won't do)
- Payment terms (net-15, upfront for first month)
- Termination clause (30 days either side)
- IP ownership (they own code, you keep methods/knowledge)
- Confidentiality (standard NDA)
- Non-compete (limited scope, reasonable duration)
- Liability cap (typically fees paid)
```

## Building a PHP Product Company

### Product Stages

```
Stage 1: Validation ($0-$1k MRR)
- Single feature
- Manual onboarding
- No automation yet
- 5-10 customers
- Founder does everything

Stage 2: Traction ($1k-$10k MRR)
- Automated onboarding
- Customer support processes
- 1-2 part-time helpers
- 20-100 customers
- Focus: retention

Stage 3: Growth ($10k-$100k MRR)
- Team: 3-10 people
- Automated marketing
- Sales process
- 500-5000 customers
- Focus: acquisition

Stage 4: Scale ($100k+ MRR)
- Full team (10+)
- Departments (engineering, sales, support)
- System reliability focus
- 10k+ customers
- Focus: efficiency
```

### Key Metrics for SaaS

```php
class SaasMetrics
{
    // Monthly Recurring Revenue
    public function mrr(array $subscriptions): float
    {
        return array_sum(array_column($subscriptions, 'monthly_price'));
    }

    // Annual Recurring Revenue
    public function arr(array $subscriptions): float
    {
        return $this->mrr($subscriptions) * 12;
    }

    // Churn Rate (monthly)
    public function churnRate(int $startCustomers, int $lostCustomers): float
    {
        return $lostCustomers / $startCustomers;
    }

    // Customer Lifetime Value
    public function ltv(float $avgRevenuePerMonth, float $monthlyChurnRate): float
    {
        return $avgRevenuePerMonth / $monthlyChurnRate;
    }

    // Customer Acquisition Cost
    public function cac(float $marketingSpend, int $newCustomers): float
    {
        return $marketingSpend / $newCustomers;
    }

    // LTV:CAC Ratio (target > 3:1)
    public function ltvToCacRatio(float $ltv, float $cac): float
    {
        return $ltv / $cac;
    }

    // Monthly burn rate
    public function runway(float $cashInBank, float $monthlyExpenses): float
    {
        return $cashInBank / $monthlyExpenses;  // Months remaining
    }
}
```

## Recommended Books

| Book | Topic | For |
|------|-------|-----|
| *The Mom Test* | Customer interviews | Entrepreneurs |
| *Lean Startup* | Build-measure-learn | Founders |
| *Zero to Sold* | Bootstrapping a SaaS | Product builders |
| *Working in Public* | Open source economics | Maintainers |
| *Hackers & Painters* | Startup mindset | Thinkers |
| *The Hard Thing About Hard Things* | Startup challenges | Leaders |
| *Company of One* | Sustainable business | Solopreneurs |
| *Inspired* | Product management | Product people |
