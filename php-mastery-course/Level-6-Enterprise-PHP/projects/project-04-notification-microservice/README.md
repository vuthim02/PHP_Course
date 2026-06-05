# Project 4: Notification Microservice with Message Queue

## Description
A notification microservice that consumes messages from RabbitMQ and dispatches via multiple channels: email (SMTP mock), SMS (Twilio mock), and push (Firebase mock). Demonstrates message-driven architecture, dead letter queues, retry logic, and template-based content generation.

## Enterprise Architecture Concepts
- **Message-Driven Architecture** — Async communication via RabbitMQ
- **Message Queue Patterns** — Work queues, topic exchanges, dead letter queues
- **Retry with Backoff** — Failed messages retried with exponential backoff
- **Dead Letter Queue** — Messages that exceed retry limit routed to DLQ
- **Template Engine** — Notification content rendered from templates
- **Multi-Channel Dispatch** — Email, SMS, Push from a single event
- **Consumer Workers** — Long-running PHP processes consuming queues
- **Supervisor/Process Management** — Docker-based worker management

## Architecture Diagram
```
┌──────────────┐     ┌─────────────────────────────────────────────────┐
│  Producer    │     │            RabbitMQ                             │
│  (any service)│────▶│                                                │
└──────────────┘     │  ┌──────────────┐  ┌──────────────────────┐    │
                      │  │  notifications  │  │  notifications.dlx │    │
                      │  │  (topic exchange)│  │  (dead letter)    │    │
                      │  └──────┬───────┘  └──────────────────────┘    │
                      │         │                                      │
                      └─────────┼──────────────────────────────────────┘
                                │
                    ┌───────────┴───────────┐
                    │    PHP Consumer        │
                    │  bin/consumer.php      │
                    │  (long-running worker) │
                    ├───────────────────────┤
                    │  NotificationDispatcher │
                    ├───────────────────────┤
                    │  ┌──────┬──────┬────┐ │
                    │  │Email │ SMS  │Push │ │
                    │  └──────┴──────┴────┘ │
                    └───────────────────────┘
```

## Setup Instructions
```bash
cd project-04-notification-microservice
docker-compose up -d --build
```

### Run the Consumer Worker
```bash
docker-compose exec app php bin/consumer.php
```

### Test by Publishing a Message
```bash
# Publish a test notification via the CLI
docker-compose exec app php bin/publish-test.php
```

### Run Migrations
```bash
docker-compose exec app php migrations/migrate.php
```

## Notification Types
| Type | Channels | Template |
|------|----------|----------|
| welcome_email | Email | Welcome template |
| password_reset | Email, SMS | Password reset link |
| order_confirmation | Email, SMS, Push | Order summary |
| weekly_digest | Email | Weekly activity summary |

## Testing & Quality
- Consumer tested with mock RabbitMQ client
- Dispatcher tested in isolation
- Template rendering tests
- Retry logic and DLQ routing verified
- PHPUnit with code coverage
