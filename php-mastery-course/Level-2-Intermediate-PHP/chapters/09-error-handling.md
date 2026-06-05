# Chapter 9: Error Handling and Exceptions

## Learning Objectives

- Create custom exception classes
- Implement try-catch-finally blocks
- Use multiple catch blocks
- Understand SPL exceptions

---

## 9.1 Exception Hierarchy

```php
<?php
namespace App\Exceptions;

// Base exception for application
class AppException extends \RuntimeException {}

// Domain exceptions
class ValidationException extends AppException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed')
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

class AuthenticationException extends AppException
{
    public function __construct(string $message = 'Unauthenticated')
    {
        parent::__construct($message, 401);
    }
}

class AuthorizationException extends AppException
{
    public function __construct(string $message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}

class NotFoundException extends AppException
{
    public function __construct(string $resource = 'Resource')
    {
        parent::__construct("{$resource} not found", 404);
    }
}

class RateLimitException extends AppException
{
    public function __construct(
        private int $retryAfter,
        string $message = 'Too many requests'
    ) {
        parent::__construct($message, 429);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
```

---

## 9.2 Try-Catch Patterns

```php
<?php
class PaymentService
{
    public function processPayment(Order $order): PaymentResult
    {
        try {
            $this->validateOrder($order);
            $charge = $this->gateway->charge(
                $order->getTotal(),
                $order->getPaymentToken()
            );
            $this->sendConfirmation($order, $charge);
            return new PaymentResult(success: true, transactionId: $charge->id);
            
        } catch (ValidationException $e) {
            // Handle validation errors
            $this->logger->warning('Payment validation failed', [
                'order' => $order->getId(),
                'errors' => $e->getErrors(),
            ]);
            throw $e; // Re-throw for controller to handle
            
        } catch (GatewayDeclinedException $e) {
            // Handle card declined
            $this->logger->info('Payment declined', [
                'order' => $order->getId(),
                'reason' => $e->getDeclineReason(),
            ]);
            $order->markAsFailed($e->getDeclineReason());
            return new PaymentResult(success: false, error: $e->getMessage());
            
        } catch (GatewayTimeoutException $e) {
            // Handle timeout - retry once
            $this->logger->warning('Gateway timeout, retrying...');
            return $this->retryPayment($order);
            
        } catch (\Throwable $e) {
            // Catch any unexpected error
            $this->logger->error('Unexpected payment error', [
                'order' => $order->getId(),
                'exception' => $e,
            ]);
            throw new PaymentProcessingException(
                'An unexpected error occurred',
                500,
                $e
            );
        } finally {
            // Always log the attempt
            $this->logger->info('Payment attempt completed');
        }
    }
}
```

---

## 9.3 Exercises

1. Create a custom exception hierarchy for a file upload system
2. Implement try-catch with multiple catch blocks for API error handling
3. Write a retry mechanism using try-catch with max attempts
4. Create a global exception handler that formats errors as JSON

---

## Further Reading

- **Doc:** [PHP Exceptions](https://www.php.net/manual/en/language.exceptions.php)
- **Doc:** [SPL Exceptions](https://www.php.net/manual/en/spl.exceptions.php)
