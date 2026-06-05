# Project 3: User Microservice (Dockerized)

## Description
A production-ready user management microservice with REST API, MySQL persistence, Redis caching, Docker Compose orchestration, health checks, and Prometheus metrics. Demonstrates microservice architecture patterns with containerized deployment.

## Enterprise Architecture Concepts
- **Microservice Architecture** — Single-responsibility service for user management
- **RESTful API** — Resource-oriented endpoints with JSON
- **Containerization** — Docker with multi-service orchestration
- **Caching** — Redis for session/token caching
- **Database Migrations** — Schema versioning
- **Health Checks** — Readiness/liveness endpoints for orchestration
- **Metrics** — Prometheus endpoint for monitoring
- **Middleware Pipeline** — Auth, logging, CORS, rate limiting

## Architecture Diagram
```
Client (curl/browser)
       │
       ▼
┌──────────────────────────────────────┐
│         Nginx (reverse proxy)        │
│    localhost:8080 → app:80           │
└──────────────────┬───────────────────┘
                   │
┌──────────────────▼───────────────────┐
│         PHP-FPM Application          │
│   public/index.php (front controller)│
│                                      │
│   Middleware Pipeline:               │
│   Auth → Logging → Router → Controller│
└──────┬─────────────────────┬─────────┘
       │                     │
       ▼                     ▼
┌──────────┐          ┌──────────┐
│  MySQL   │          │  Redis   │
│  :3306   │          │  :6379   │
└──────────┘          └──────────┘
```

## Setup Instructions
```bash
cd project-03-user-microservice
docker-compose up -d --build
```

### Verify
```bash
curl http://localhost:8080/api/health
curl http://localhost:8080/api/users
```

### Run Migrations
```bash
docker-compose exec app php migrations/migrate.php
```

### Run Tests
```bash
docker-compose exec app php vendor/bin/phpunit tests/
```

## API Endpoints
| Method | Path | Description |
|--------|------|-------------|
| GET | /api/health | Health check |
| GET | /api/metrics | Prometheus metrics |
| POST | /api/auth/register | Register user |
| POST | /api/auth/login | Login |
| GET | /api/users | List users (paginated) |
| GET | /api/users/{id} | Get user |
| PUT | /api/users/{id} | Update user |
| DELETE | /api/users/{id} | Delete user |
| GET | /api/users/{id}/profile | Get profile |

## Testing & Quality
- PHPUnit tests for controllers, services, models
- Middleware tested in isolation
- Docker-based integration tests
- PSR-4 autoloading, strict types
- Error handling with proper HTTP status codes
