# Chapter 11: Distributed Systems (CAP Theorem)

## Learning Objectives

- Understand CAP theorem
- Implement distributed consensus
- Handle network partitions
- Apply Saga pattern

---

## 11.1 Distributed Transactions (Saga Pattern)

```php
<?php
// Saga pattern for distributed transactions
class OrderSaga
{
    private array $steps = [];
    private array $compensations = [];
    private int $currentStep = 0;

    public function addStep(callable $action, callable $compensation): void
    {
        $this->steps[] = $action;
        $this->compensations[] = $compensation;
    }

    public function execute(): void
    {
        try {
            foreach ($this->steps as $i => $step) {
                $this->currentStep = $i;
                $step();
            }
        } catch (\Exception $e) {
            $this->rollback();
            throw new SagaException('Saga failed', 0, $e);
        }
    }

    private function rollback(): void
    {
        for ($i = $this->currentStep; $i >= 0; $i--) {
            try {
                ($this->compensations[$i])();
            } catch (\Exception $e) {
                error_log("Compensation failed at step {$i}: {$e->getMessage()}");
                // Log for manual intervention
            }
        }
    }
}

// Usage: Place order saga
$saga = new OrderSaga();

// Step 1: Reserve inventory
$saga->addStep(
    action: fn() => $inventory->reserve($orderId, $items),
    compensation: fn() => $inventory->release($orderId),
);

// Step 2: Charge payment
$saga->addStep(
    action: fn() => $payment->charge($orderId, $total),
    compensation: fn() => $payment->refund($orderId),
);

// Step 3: Update order status
$saga->addStep(
    action: fn() => $orders->confirm($orderId),
    compensation: fn() => $orders->cancel($orderId),
);

$saga->execute();
```

---

## 11.2 Exercises

1. Implement a Saga pattern for order processing across 3 microservices
2. Handle network partitions with retry and timeout strategies
3. Implement a distributed lock using Redis
4. Build a two-phase commit (2PC) coordinator

---

## Further Reading

- **Book:** "Designing Distributed Systems" by Brendan Burns
- **Doc:** [Saga Pattern](https://microservices.io/patterns/data/saga.html)
