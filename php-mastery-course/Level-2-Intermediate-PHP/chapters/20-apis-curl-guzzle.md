# Chapter 20: Working with APIs (cURL, Guzzle)

## Learning Objectives

- Make HTTP requests with cURL and Guzzle
- Handle API responses and errors
- Implement retry logic
- Build a reusable HTTP client

---

## 20.1 cURL Requests

```php
<?php
class CurlClient
{
    public function get(string $url, array $headers = []): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->buildHeaders($headers),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new HttpException("cURL error: {$error}");
        }

        return [
            'status' => $httpCode,
            'body' => json_decode($response, true),
        ];
    }

    public function post(string $url, array $data, array $headers = []): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->buildHeaders(array_merge(
                $headers,
                ['Content-Type: application/json']
            )),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'body' => json_decode($response, true),
        ];
    }

    private function buildHeaders(array $headers): array
    {
        $result = [];
        foreach ($headers as $key => $value) {
            $result[] = "{$key}: {$value}";
        }
        return $result;
    }
}
```

---

## 20.2 Guzzle HTTP Client

```php
<?php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private Client $client;

    public function __construct(array $config = [])
    {
        $stack = HandlerStack::create();

        // Retry middleware
        $stack->push(Middleware::retry(
            function (int $retries, RequestInterface $request, ?ResponseInterface $response, ?RequestException $exception) {
                // Retry on 429, 503 or connection errors, max 3 times
                if ($retries >= 3) {
                    return false;
                }

                if ($response && in_array($response->getStatusCode(), [429, 503])) {
                    return true;
                }

                if ($exception && $exception->getCode() === 0) {
                    return true; // Connection error
                }

                return false;
            },
            function (int $retries) {
                // Exponential backoff
                return 1000 * (2 ** $retries);
            }
        ));

        $this->client = new Client(array_merge([
            'handler' => $stack,
            'timeout' => 30.0,
            'connect_timeout' => 5.0,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'MyApp/1.0',
            ],
        ], $config));
    }

    public function get(string $url, array $options = []): array
    {
        $response = $this->client->get($url, $options);
        return $this->handleResponse($response);
    }

    public function post(string $url, array $data = [], array $options = []): array
    {
        $options['json'] = $data;
        $response = $this->client->post($url, $options);
        return $this->handleResponse($response);
    }

    private function handleResponse(ResponseInterface $response): array
    {
        $body = json_decode($response->getBody(), true);
        $status = $response->getStatusCode();

        if ($status >= 400) {
            throw new ApiException(
                $body['error']['message'] ?? 'API request failed',
                $status
            );
        }

        return [
            'status' => $status,
            'data' => $body,
            'headers' => $response->getHeaders(),
        ];
    }
}
```

---

## 20.3 Exercises

1. Build a client that fetches data from the GitHub API
2. Add exponential backoff retry logic for rate-limited endpoints
3. Implement OAuth 2.0 client credentials flow
4. Create a concurrent request handler using Guzzle promises

---

## Further Reading

- **Doc:** [Guzzle Documentation](https://docs.guzzlephp.org/)
- **Doc:** [PHP cURL](https://www.php.net/manual/en/book.curl.php)
