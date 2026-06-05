# Career Mastery — From Senior to Elite Engineer

## The Elite Engineer Mindset

### What separates elite from senior?

| Dimension | Senior Engineer | Elite Engineer |
|-----------|----------------|----------------|
| **Scope** | Solves problems within the team | Solves problems across the organization |
| **Focus** | Technical execution | Technical strategy |
| **Communication** | Communicates decisions | Drives alignment across teams |
| **Impact** | Delivers features | Defines architecture that enables others |
| **Time horizon** | This quarter | Next 1-3 years |
| **Decision making** | Chooses between options | Defines the options and criteria |
| **Mentorship** | Reviews code | Grows other engineers |
| **Business** | Understands requirements | Understands business goals and trade-offs |

## Technical Strategy

### Architecture Decision Records (ADRs)

```markdown
# ADR-001: Use PostgreSQL instead of MySQL

## Status
Accepted

## Context
We need a relational database for the new billing system.
Requirements: JSON support, advanced indexing, CTEs.

## Decision
We will use PostgreSQL 15+ as our primary database.

## Consequences
### Positive
- Native JSONB with indexing
- CTEs and window functions for complex reporting
- Better compliance with SQL standards

### Negative
- Team needs training on PostgreSQL-specific features
- Migration from existing MySQL tables required
- Hosting costs may increase (RDS for PostgreSQL is ~15% more)

## Alternatives Considered
- MySQL 8.0: Lacks advanced JSON queries
- SQLite: Not suitable for multi-server deployment
```

### Technical Strategy Template

```
1. Current State Assessment
   - What's working?
   - What's painful?
   - What are the constraints?

2. Goals
   - Business outcomes (not technical outputs)
   - Measurable success criteria

3. Options
   - Option A (conservative): Minimum risk, known trade-offs
   - Option B (innovative): Higher risk, higher reward
   - Option C (transformative): Major change, major upside

4. Recommendation
   - Why this option
   - Migration path
   - Risk mitigation

5. Timeline
   - Phases (6-12 week increments)
   - Milestones
   - Rollback criteria
```

## Leadership Skills

### Technical Leadership Without Authority

```php
// Influence through technical excellence, not hierarchy

// 1. Build credibility through quality work
// 2. Write ADRs to codify decisions
// 3. Conduct RFC-style proposals
// 4. Lead by example (code reviews, documentation)
// 5. Create shared context (diagrams, runbooks)
// 6. Focus on outcomes, not output
```

### Running Technical Discussions

```
1. State the problem clearly (5 min)
2. Present options with data (10 min)
3. Discuss trade-offs (15 min)
4. Make a decision (5 min)
5. Document and communicate (5 min)

Key rules:
- Data beats opinions
- Disagree and commit
- No decision is a decision (decide to defer)
```

### Growing Other Engineers

```
Mentorship Modes:
- Coaching: Ask questions, let them find answers
- Sponsorship: Open doors, give visibility
- Teaching: Share knowledge directly
- Challenging: Give stretch assignments

Key practice: "Rubber duck" pairing
- Let them drive
- Ask "What do you think?" before giving answers
- Provide context, not solutions
```

## Building System Reliability

### Service Level Objectives (SLOs)

```php
// Define reliability targets in measurable terms

// Example SLOs for a payment API:
// - Availability: 99.95% (≤22 min downtime/month)
// - Latency P95: <500ms
// - Latency P99: <2s
// - Error rate: <0.1%

class SloTracker
{
    public function __construct(
        private string $window = '30d'
    ) {}

    public function checkAvailability(array $requests): float
    {
        $total = count($requests);
        $errors = count(array_filter(
            $requests,
            fn($r) => $r['status'] >= 500
        ));

        return ($total - $errors) / $total * 100;
    }

    public function checkLatency(array $requests, float $percentile): float
    {
        $latencies = array_column($requests, 'duration_ms');
        sort($latencies);

        $index = (int) ceil($percentile / 100 * count($latencies));
        return $latencies[$index - 1];
    }

    public function errorBudget(): float
    {
        // Error budget = (1 - SLO) * total time window
        // If SLO = 99.95%, error budget = 0.05% of time
        // Monthly: 21.6 minutes of allowed downtime
        return 0.9995;  // SLO
    }
}
```

### Error Budget Policy

```
SLO: 99.95%
Error budget: 0.05% = ~22 minutes/month

Policy:
- Within budget: Ship features, deploy normally
- 50% consumed: Increase testing, slow deploys
- 75% consumed: Freeze deploys, focus on reliability
- 100% consumed: All hands on reliability

Spending error budget is OK — it means we're iterating fast.
But overspending means we're moving too fast.
```

## Cost Optimization

### Cloud Cost Analysis

```php
class CostOptimizer
{
    // Right-sizing for PHP-FPM
    public function calculateFpmWorkers(int $ramMB, int $memoryPerWorkerMB): int
    {
        // Reserve 25% for OS and overhead
        $available = $ramMB * 0.75;
        return (int) floor($available / $memoryPerWorkerMB);
    }

    // Reserved vs On-demand pricing
    public function reservedInstanceValue(
        float $onDemandPrice,
        float $reservedPrice,
        int $runningHoursPerYear = 8760,  // 24/7
        int $commitmentYears = 1
    ): float {
        $onDemandTotal = $onDemandPrice * $runningHoursPerYear;
        $reservedTotal = $reservedPrice * $runningHoursPerYear * $commitmentYears;
        return $onDemandTotal - $reservedTotal;
    }

    // Cache effectiveness
    public function cacheHitSavings(
        int $totalRequests,
        float $hitRate,     // 0-1
        float $originCostMs,
        float $cacheCostMs
    ): float {
        $hits = $totalRequests * $hitRate;
        $misses = $totalRequests * (1 - $hitRate);

        $costWithoutCache = $totalRequests * $originCostMs;
        $costWithCache = ($hits * $cacheCostMs) + ($misses * $originCostMs);

        return ($costWithoutCache - $costWithCache) / $costWithoutCache * 100;
    }
}
```

## Security Architecture

### Threat Modeling (STRIDE per Component)

```
S — Spoofing: Can someone impersonate a user/service?
T — Tampering: Can data be modified in transit or at rest?
R — Repudiation: Can a user deny an action?
I — Information Disclosure: Can sensitive data leak?
D — Denial of Service: Can the service be overwhelmed?
E — Elevation of Privilege: Can a user gain higher privileges?
```

```php
// Security architecture example — defense in depth
class SecurePaymentProcessor
{
    public function __construct(
        private PaymentGateway $gateway,
        private AuditLogger $logger,
        private RateLimiter $rateLimiter,
        private EncryptionService $encryption,
    ) {}

    public function processPayment(PaymentRequest $request): PaymentResult
    {
        // 1. Rate limit (DoS protection)
        $this->rateLimiter->check($request->userId, 'payment');

        // 2. Validate input (spoofing/tampering)
        $validated = $this->validateRequest($request);

        // 3. Encrypt sensitive data (disclosure)
        $encrypted = $this->encryption->encrypt($validated);

        // 4. Process with retry logic
        $result = $this->gateway->charge($encrypted);

        // 5. Audit log (repudiation)
        $this->logger->log('payment.processed', [
            'userId' => $request->userId,
            'amount' => $request->amount,
            'status' => $result->status,
            'timestamp' => now(),
        ]);

        return $result;
    }
}
```

## Entrepreneurship & Consulting

### Rate Setting

```php
// Calculate your hourly rate as a consultant
function calculateRate(float $targetAnnual, int $billableWeeks = 44): float
{
    $overhead = $targetAnnual * 0.30;  // Taxes, insurance, tools
    $total = $targetAnnual + $overhead;
    $hours = $billableWeeks * 40;       // Max billable hours
    return $total / $hours;
}

// Example:
$rate = calculateRate(150_000);  // ~$132/hr for 150k target
// Realistic billable: 20-30h/week (marketing, admin, rest)
// Effective rate: ~$200-300/hr
```

### Product vs Service

| Aspect | Consulting/Service | Product/SaaS |
|--------|-------------------|--------------|
| **Revenue** | Hourly/project (linear) | Recurring (exponential) |
| **Scalability** | Limited by time | Unlimited |
| **Risk** | Low | High |
| **Time to revenue** | Weeks | Months |
| **Lifestyle** | Predictable | Unpredictable |
| **Exit** | Agency sale (2-4x profit) | Multiple (5-20x ARR) |

## Conference Speaking

### CFP Process

```
1. Choose topic you're passionate about
2. Write a compelling abstract (3-5 sentences)
3. Include takeaway (what will attendees learn?)
4. Submit to CFP (Conf Calls, PaperCall)
5. If rejected, try 5 more conferences
6. If accepted, prepare and rehearse

Tips:
- Start with local meetups
- Speak at work first (lunch and learn)
- Submit to PHP conferences: Laracon, SymfonyCon, PHP[TEK]
- Record yourself, watch it back
- Don't read slides — tell stories
```

### Talk Structure

```
Intro (2min)
  - Who you are
  - Why this topic matters
  - What you'll cover

Problem (5min)
  - The pain
  - Real example
  - Why it's hard

Solution (15min)
  - Live code / demo
  - Key insight
  - Architecture

Trade-offs (5min)
  - When NOT to use this
  - Alternatives

Q&A (5min)
```

## Continuous Growth Plan

### 90-Day Growth Cycle

```
Days 1-30: Deep Dive
  - Read 2 technical books
  - Contribute to open source
  - Learn one new technology (try building something)

Days 31-60: Apply
  - Teach something (blog post, talk, pair programming)
  - Build a small project using new skills
  - Review code in unfamiliar area

Days 61-90: Teach
  - Write an ADR or technical proposal
  - Give a brown bag / lunch and learn
  - Mentor a junior engineer

Repeat every quarter with new topics.
```

### Areas to Master

| Area | Resources |
|------|-----------|
| System Design | "Designing Data-Intensive Applications" |
| Distributed Systems | MIT 6.824 (YouTube lectures) |
| PHP Internals | php-src source code, PHP Internals Book |
| Business | "The Mom Test", "Lean Startup" |
| Leadership | "The Manager's Path", "Radical Candor" |
| Communication | "Crucial Conversations" |
| Writing | "On Writing Well" |
| Architecture | "Clean Architecture", "DDD" |
