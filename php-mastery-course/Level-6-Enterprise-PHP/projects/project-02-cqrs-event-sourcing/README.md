# Project 2: CQRS + Event Sourcing Bank Account

## Description
A bank account system built with Command Query Responsibility Segregation (CQRS) and Event Sourcing. All state changes are stored as an immutable event stream. Read models are projected from events for query optimization.

## Enterprise Architecture Concepts
- **CQRS** — Separate command (write) and query (read) models
- **Event Sourcing** — State derived from event stream; events are the source of truth
- **Event Store** — Append-only event log (JSON file per stream)
- **Projections** — Read models rebuilt by replaying events
- **Command Bus** — Dispatches commands to handlers
- **Event Bus** — Dispatches events to projectors
- **Aggregate** — Account aggregate rebuilt from event stream

## Architecture Diagram
```
                    ┌──────────────────────┐
                    │    Command Bus        │
                    │ OpenAccountCommand    │
                    │ DepositMoneyCommand   │
                    │ WithdrawMoneyCommand  │
                    └──────┬───────────────┘
                           │
                    ┌──────▼───────────────┐
                    │    Account Aggregate  │
                    │  (Event Sourced)     │
                    │  apply + handle      │
                    └──────┬───────────────┘
                           │
                    ┌──────▼───────────────┐
                    │    Event Store        │
                    │  events/account-*.json│
                    └──────┬───────────────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
     ┌────────▼───┐ ┌─────▼─────┐ ┌───▼──────────┐
     │Balance      │ │Transaction│ │   Query Bus   │
     │Projection   │ │Projection │ │ GetBalance    │
     │(read model) │ │(read model)│ │ GetHistory    │
     └─────────────┘ └───────────┘ └──────┬───────┘
                                          │
                                   ┌──────▼───────┐
                                   │   CLI Demo    │
                                   └──────────────┘
```

## Setup Instructions
```bash
cd project-02-cqrs-event-sourcing
composer install
```

### Run CLI Demo
```bash
php examples/cli-bank.php
```

### Run Tests
```bash
php vendor/bin/phpunit tests/
```

## How to Use
- **Commands:** `open-account <owner> <currency>`, `deposit <id> <amount>`, `withdraw <id> <amount>`, `close <id>`
- **Queries:** `balance <id>`, `history <id>`
- **Events:** Every command produces events stored in `data/events/<id>.json`
- **Projections:** Rebuilt on each query by replaying the event stream

## Testing & Quality
- Unit tests for aggregate behavior (event sourcing replay)
- Integration tests for event store read/write
- Projection tests — verify read models reflect events
- Test for idempotency, concurrency (expected version)
