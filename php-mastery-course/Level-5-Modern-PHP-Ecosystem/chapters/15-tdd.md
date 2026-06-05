# Chapter 15: Test-Driven Development

## Learning Objectives

- Apply red-green-refactor cycle
- Design testable code
- Write tests before implementation
- Build features incrementally

---

```mermaid
flowchart TD
    A[RED: Write failing test] -->|Test describes desired behavior| B[Test fails]
    B -->|Expected - proves test works| C[GREEN: Write minimal code]
    C -->|Make test pass quickly| D[Test passes]
    D --> E{Refactor?}
    E -->|Yes| F[REFACTOR: Clean code]
    F -->|Improve design, keep tests green| G[Tests still pass]
    G --> H[Add next scenario]
    H --> A
    E -->|No| H

    style A fill:#e74c3c,color:#fff
    style B fill:#e74c3c,color:#fff
    style C fill:#2ecc71,color:#fff
    style D fill:#2ecc71,color:#fff
    style F fill:#4a90d9,color:#fff
```

```mermaid
sequenceDiagram
    participant Dev as Developer
    participant Test as Test Suite
    participant Code as Application Code

    Dev->>Test: Write test for new feature
    Test-->>Dev: RED - Test fails (no code yet)
    Dev->>Code: Write minimal implementation
    Dev->>Test: Run test again
    Test-->>Dev: GREEN - Test passes
    Dev->>Code: Refactor and improve
    Dev->>Test: Run all tests
    Test-->>Dev: All GREEN - Safe to commit
    Note over Dev,Test: Incrementally add more scenarios
```

## 15.1 TDD Cycle

```php
<?php
// 1. RED — Write a failing test
it('calculates discount for loyal customers', function () {
    $pricing = new PricingService();
    $result = $pricing->calculateDiscount(100.00, 'loyal');
    
    expect($result)->toBe(90.00); // 10% off
});

// 2. GREEN — Write minimal code to pass
class PricingService
{
    public function calculateDiscount(float $amount, string $tier): float
    {
        if ($tier === 'loyal') {
            return $amount * 0.9;
        }
        return $amount;
    }
}

// 3. REFACTOR — Improve code while tests pass
class PricingService
{
    private array $discounts = [
        'loyal' => 0.10,
        'vip' => 0.20,
        'wholesale' => 0.15,
    ];

    public function calculateDiscount(float $amount, string $tier): float
    {
        $discount = $this->discounts[$tier] ?? 0;
        return $amount * (1 - $discount);
    }
}

// Adding new feature
// 1. RED: Test higher discount tiers
it('gives VIP customers 20% off', function () {
    $pricing = new PricingService();
    expect($pricing->calculateDiscount(100.00, 'vip'))->toBe(80.00);
});

it('gives wholesale customers 15% off', function () {
    $pricing = new PricingService();
    expect($pricing->calculateDiscount(100.00, 'wholesale'))->toBe(85.00);
});

it('gives no discount for regular customers', function () {
    $pricing = new PricingService();
    expect($pricing->calculateDiscount(100.00, 'regular'))->toBe(100.00);
});

// 2. GREEN: Already passes with refactored code
// 3. REFACTOR: No changes needed
```

---

## 15.2 Exercises

1. Build a shopping cart using TDD (add item, remove item, calculate total)
2. Implement a user registration service with TDD
3. Create a validation rule engine with TDD
4. Refactor existing untested code with tests first

---

## Further Reading

- **Book:** "Test-Driven Development by Example" by Kent Beck
