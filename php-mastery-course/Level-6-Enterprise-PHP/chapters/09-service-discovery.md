# Chapter 9: Service Discovery

## Learning Objectives

- Implement service registration
- Handle health checks
- Use Consul for discovery
- Build resilient clients

---

## 9.1 Service Discovery with Consul

```php
<?php
class ConsulServiceDiscovery
{
    private string $consulHost = 'http://localhost:8500';

    public function registerService(string $name, string $host, int $port, array $tags = []): void
    {
        $registration = [
            'Name' => $name,
            'Address' => $host,
            'Port' => $port,
            'Tags' => $tags,
            'Check' => [
                'HTTP' => "http://{$host}:{$port}/health",
                'Interval' => '10s',
                'Timeout' => '5s',
                'DeregisterCriticalServiceAfter' => '30s',
            ],
        ];

        $this->put("/v1/agent/service/register", $registration);
    }

    public function discoverService(string $name): array
    {
        $services = $this->get("/v1/health/service/{$name}?passing");
        
        if (empty($services)) {
            throw new ServiceUnavailableException("No healthy instances of {$name}");
        }

        // Pick a random healthy instance
        $service = $services[array_rand($services)];
        
        return [
            'host' => $service['Service']['Address'],
            'port' => $service['Service']['Port'],
        ];
    }

    public function discoverAll(string $name): array
    {
        $services = $this->get("/v1/health/service/{$name}?passing");
        
        return array_map(fn($s) => [
            'host' => $s['Service']['Address'],
            'port' => $s['Service']['Port'],
            'tags' => $s['Service']['Tags'],
        ], $services);
    }

    private function get(string $path): array
    {
        $response = file_get_contents("{$this->consulHost}{$path}");
        return json_decode($response, true) ?? [];
    }

    private function put(string $path, array $data): void
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'PUT',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data),
            ],
        ]);
        file_get_contents("{$this->consulHost}{$path}", false, $context);
    }
}

// Resilient HTTP client with service discovery
class ServiceClient
{
    public function __construct(
        private ConsulServiceDiscovery $discovery,
        private string $serviceName,
    ) {}

    public function request(string $method, string $path, array $options = []): array
    {
        $maxRetries = 3;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $instance = $this->discovery->discoverService($this->serviceName);
                $url = "http://{$instance['host']}:{$instance['port']}{$path}";

                $client = new HttpClient();
                return $client->request($method, $url, $options);

            } catch (ServiceUnavailableException $e) {
                $attempt++;
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
                sleep(pow(2, $attempt));
            }
        }

        throw new ServiceUnavailableException("All retries exhausted");
    }
}
```

---

## 9.2 Exercises

1. Set up Consul and register a PHP service
2. Implement health check endpoints
3. Build a client that discovers services dynamically
4. Add load balancing across service instances

---

## Further Reading

- **Doc:** [Consul](https://www.consul.io/docs)
- **Doc:** [Service Discovery Patterns](https://microservices.io/patterns/server-side-discovery.html)
