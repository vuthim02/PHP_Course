# Chapter 2: PSR Standards

## Learning Objectives

- Understand all major PSR standards
- Apply PSR-12 coding style
- Implement PSR-4 autoloading
- Use PSR-7 HTTP messages
- Understand PSR-11 container interface

---

## 2.1 Key PSR Standards

| PSR | Name | Purpose |
|-----|------|---------|
| PSR-1 | Basic Coding Standard | PHP tags, side effects, naming |
| PSR-3 | Logger Interface | Standard logging interface |
| PSR-4 | Autoloading | Namespace to filepath mapping |
| PSR-6 | Caching Interface | Cache pools and items |
| PSR-7 | HTTP Message Interface | Request/response objects |
| PSR-11 | Container Interface | Dependency injection container |
| PSR-12 | Extended Coding Style | Code formatting rules |
| PSR-14 | Event Dispatcher | Event handling |
| PSR-15 | HTTP Handlers | Request handlers/middleware |
| PSR-18 | HTTP Client | Sending HTTP requests |

### PSR-12 Example

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentInterface;
use App\Exceptions\PaymentException;

class PaymentService implements PaymentInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly HttpClient $client,
    ) {
    }

    public function process(array $paymentData): PaymentResult
    {
        if (empty($paymentData['amount'])) {
            throw new PaymentException('Amount is required');
        }

        $response = $this->client->post('/charges', [
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'usd',
        ]);

        return new PaymentResult($response);
    }
}
```

---

## 2.2 PSR-7 Example

```php
<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new \GuzzleHttp\Psr7\Response();
        $response->getBody()->write(json_encode(['status' => 'ok']));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
```

---

## 2.3 Exercises

1. Format a PHP file to comply with PSR-12
2. Implement a PSR-3 Logger interface
3. Create a PSR-11 compliant DI container
4. Use PSR-7 messages in a simple application
5. Implement PSR-15 middleware

---

## Further Reading

- **Doc:** [PHP-FIG PSRs](https://www.php-fig.org/psr/)
- **Doc:** [PSR-12](https://www.php-fig.org/psr/psr-12/)
