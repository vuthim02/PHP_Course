# Chapter 18: Monitoring and Alerting (Prometheus, Grafana)

## Learning Objectives

- Export PHP metrics to Prometheus
- Create Grafana dashboards
- Set up alerting rules
- Implement APM tracing

---

## 18.1 Prometheus Metrics

```php
<?php
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Prometheus\RenderTextFormat;

class MetricsExporter
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        Redis::setDefaultOptions(['host' => $_ENV['REDIS_HOST']]);
        $this->registry = CollectorRegistry::getDefault();
    }

    public function registerCounters(): void
    {
        $counter = $this->registry->registerCounter(
            'app',
            'http_requests_total',
            'Total HTTP requests',
            ['method', 'endpoint', 'status']
        );
        $counter->incBy(1, ['GET', '/api/users', '200']);
    }

    public function registerHistograms(): void
    {
        $histogram = $this->registry->registerHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration',
            ['method', 'endpoint'],
            [0.01, 0.05, 0.1, 0.5, 1, 2, 5]
        );
        $histogram->observe(0.15, ['GET', '/api/users']);
    }

    public function registerGauges(): void
    {
        $gauge = $this->registry->registerGauge(
            'app',
            'active_users',
            'Currently active users'
        );
        $gauge->set(42);

        $queueGauge = $this->registry->registerGauge(
            'app',
            'queue_size',
            'Current queue size',
            ['queue']
        );
        $queueGauge->set(10, ['email']);
    }

    public function render(): string
    {
        $renderer = new RenderTextFormat();
        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}

// Middleware to collect metrics
class MetricsMiddleware
{
    public function __construct(private MetricsExporter $metrics) {}

    public function handle(callable $next): void
    {
        $start = microtime(true);

        $next();

        $duration = microtime(true) - $start;
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $endpoint = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $status = http_response_code();

        $this->metrics->recordRequest($method, $endpoint, $status, $duration);
    }
}

// /metrics endpoint for Prometheus
$router->get('/metrics', function () use ($metrics) {
    header('Content-Type: text/plain');
    echo $metrics->render();
});
```

---

## 18.2 Exercises

1. Export application metrics to Prometheus (request count, duration, queue size)
2. Create Grafana dashboard for PHP application performance
3. Set up alerting rules (high error rate, slow responses, queue backlog)
4. Implement distributed tracing with OpenTelemetry

---

## Further Reading

- **Doc:** [Prometheus PHP](https://github.com/prometheus/client_php)
- **Doc:** [Grafana](https://grafana.com/docs/)
