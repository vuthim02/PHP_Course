# Project 1: High-Traffic URL Shortener

A production-grade URL shortener engineered for millions of URLs and billions of redirects. Demonstrates system design patterns used at companies like Bitly, TinyURL, and Rebrandly.

## Elite PHP Engineer Concepts

- **System Design**: Cache-aside pattern, write-behind queue, partition-ready ID generation
- **High-Performance PHP**: Connection pooling, async queue processing, bloom filters
- **Redis Deep Usage**: Caching, rate limiting, queue backend, distributed locks
- **Database Scaling**: Read replicas (simulated), connection pooling, query optimization
- **Async Processing**: Job queues for click analytics, delayed writes
- **Rate Limiting**: Token bucket algorithm, per-IP and per-user limits

## Architecture

```
┌─────────────┐     ┌────────────────┐     ┌───────────────┐
│   Client     │────▶│  Rate Limit    │────▶│   Redis        │
│ (Browser/API)│     │  Middleware    │     │   Cache Layer  │
└─────────────┘     └────────────────┘     └───────┬───────┘
                                                    │
                                                    ▼
┌─────────────┐     ┌────────────────┐     ┌───────────────┐
│  CDN Edge   │◀────│   Response     │◀────│  URL Service   │
│ (301/302)    │     │   (redirect)   │     │  (Resolver)    │
└─────────────┘     └────────────────┘     └───────┬───────┘
                                                    │
                                          ┌─────────▼────────┐
                                          │  MySQL Primary    │
                                          │  + Read Replicas  │
                                          └─────────┬────────┘
                                                    │
                                          ┌─────────▼────────┐
                                          │  Analytics Queue  │
                                          │  (Async via Redis)│
                                          └──────────────────┘
```

### Cache-Aside Pattern

1. Client requests short code → check Redis cache (O(1))
2. Cache hit → return 301/302 immediately
3. Cache miss → query MySQL → populate Redis → return
4. TTL-based expiration for stale cache eviction

### Write-Behind Queue for Analytics

- Each click enqueues an analytics job (async, non-blocking)
- Worker processes batch inserts every 5s or 1000 events
- Reduces MySQL write load by ~100x

## Performance Considerations

- **Redis**: Sub-millisecond cache lookups, 100k+ ops/sec
- **Bloom Filter**: O(k) memory-efficient check for custom alias collisions
- **ID Generation**: Snowflake-inspired 64-bit IDs with timestamp + worker + sequence
- **Connection Pooling**: Reuse database connections via persistent PDO
- **Async Analytics**: Zero-latency click tracking for users
- **CDN Ready**: Cache-Control headers for edge caching of redirects

## Setup

```bash
composer install
cp config/config.example.php config/config.php
# Edit config.php with your Redis/MySQL credentials
php -S localhost:8080 -t public
```

## Running Benchmarks

```bash
composer benchmark
```

## Extending

- Add custom alias validation rules in `UrlService`
- Swap queue backend to RabbitMQ/Amazon SQS
- Add gRPC service for internal microservice communication
- Implement GeoIP-based analytics in `AnalyticsService`

## Real-World Use Cases

- Branded link shortener for marketing campaigns
- Internal URL redirect service for enterprise
- QR code generation backend
- Link management platform with analytics
