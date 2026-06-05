# Chapter 9: Implementing GraphQL

## Learning Objectives

- Integrate graphql-php in an application
- Build type system programmatically
- Handle authentication and authorization
- Implement DataLoader for N+1 prevention

---

## 9.1 GraphQL Server

```php
<?php
// Entry point
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

class GraphQLServer
{
    public function __construct(
        private Schema $schema,
        private ?array $middleware = []
    ) {}

    public function handle(): never
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $query = $input['query'] ?? '';
        $variables = $input['variables'] ?? null;
        $operationName = $input['operationName'] ?? null;

        $context = ['user' => $this->authenticate()];

        // Run middleware
        foreach ($this->middleware as $mw) {
            $mw($context);
        }

        $result = GraphQL::executeQuery(
            $this->schema,
            $query,
            null,
            $context,
            $variables,
            $operationName
        );

        $output = $result->toArray();
        
        header('Content-Type: application/json');
        echo json_encode($output);
        exit;
    }

    private function authenticate(): ?array
    {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        // Validate token...
        return null;
    }
}

// DataLoader for N+1 prevention
class DataLoader
{
    private array $loaders = [];

    public function loader(string $name, callable $batchFn): callable
    {
        if (!isset($this->loaders[$name])) {
            $this->loaders[$name] = ['queue' => [], 'fn' => $batchFn];
        }

        return function ($key) use ($name) {
            $this->loaders[$name]['queue'][] = $key;
            
            return function () use ($name) {
                $loader = $this->loaders[$name];
                $keys = array_unique($loader['queue']);
                
                if (!empty($keys)) {
                    $results = ($loader['fn'])($keys);
                    $this->loaders[$name]['queue'] = [];
                    return $results;
                }
                
                return [];
            };
        };
    }

    public function resolveAll(): void
    {
        foreach ($this->loaders as &$loader) {
            if (!empty($loader['queue'])) {
                $keys = array_unique($loader['queue']);
                ($loader['fn'])($keys);
                $loader['queue'] = [];
            }
        }
    }
}
```

---

## 9.2 Exercises

1. Build a complete GraphQL API for a blog platform
2. Add DataLoader to solve N+1 queries
3. Implement authentication and authorization in resolvers
4. Add pagination with Relay-style connections

---

## Further Reading

- **Doc:** [GraphQL PHP DataLoader](https://github.com/overblog/dataloader-php)
