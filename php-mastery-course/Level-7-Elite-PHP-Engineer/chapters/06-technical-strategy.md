# Chapter 6: Technical Strategy

## Learning Objectives

- Write Architecture Decision Records (ADRs)
- Create technical roadmaps
- Manage technical debt
- Lead architecture reviews

---

## 6.1 Architecture Decision Records

```markdown
# ADR-001: Use Event-Driven Architecture for Order Processing

## Status
Accepted

## Context
The current order processing is synchronous, causing timeout issues
during high traffic. We need to decouple order placement from
downstream processing.

## Decision
We will adopt an event-driven architecture using RabbitMQ:
1. Order service publishes OrderPlaced event
2. Inventory, Payment, Notification services consume events
3. Each service processes independently

## Consequences
- Positive: Better scalability, fault isolation
- Positive: Independent deployment
- Negative: Eventual consistency
- Negative: Need event monitoring

## Compliance
- All new order flows must publish events
- Events must follow schema v1.0
- Services must handle duplicate events idempotently

---

# ADR-002: Adopt PHP 8.3 for New Services

## Status
Accepted

## Context
New services need typed properties, readonly classes, and enums.
PHP 8.3 provides these features plus JIT improvements.

## Decision
All new microservices will use PHP 8.3.
Existing services will be upgraded within 6 months.

## Consequences
- Positive: Better type safety, performance
- Positive: Reduced boilerplate
- Negative: Migration effort for existing services
- Negative: Third-party library compatibility checks
```

---

## 6.2 Exercises

1. Write an ADR for migrating from MySQL to PostgreSQL
2. Create a technical roadmap for the next quarter
3. Conduct an architecture review of an existing system
4. Create a tech debt register with priority levels

---

## Further Reading

- **Doc:** [ADR Template](https://adr.github.io/)
- **Book:** "Software Architecture in Practice" by Bass, Clements, Kazman
