# Chapter 8: GraphQL Fundamentals

## Learning Objectives

- Understand GraphQL schema design and type system
- Write queries, mutations, and subscriptions
- Implement resolvers with batching and DataLoader
- Handle N+1 query problems
- Add pagination with connections
- Implement authentication and authorization
- Compare GraphQL with REST for different use cases

---

## 8.1 Schema Definition

```php
<?php
// Schema definition using graphql-php (webonyx/graphql-php)
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

$userType = new ObjectType([
    'name' => 'User',
    'fields' => [
        'id' => Type::id(),
        'name' => Type::string(),
        'email' => Type::string(),
        'posts' => [
            'type' => Type::listOf($postType),
            'resolve' => fn(User $user) => $user->posts(),
        ],
    ],
]);

$postType = new ObjectType([
    'name' => 'Post',
    'fields' => [
        'id' => Type::id(),
        'title' => Type::string(),
        'content' => Type::string(),
        'author' => [
            'type' => $userType,
            'resolve' => fn(Post $post) => $post->author(),
        ],
    ],
]);

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'users' => [
            'type' => Type::listOf($userType),
            'resolve' => fn() => User::all(),
        ],
        'user' => [
            'type' => $userType,
            'args' => [
                'id' => Type::nonNull(Type::id()),
            ],
            'resolve' => fn($root, array $args) => User::find($args['id']),
        ],
    ],
]);

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'createUser' => [
            'type' => $userType,
            'args' => [
                'name' => Type::nonNull(Type::string()),
                'email' => Type::nonNull(Type::string()),
            ],
            'resolve' => fn($root, array $args) => User::create($args),
        ],
    ],
]);

$schema = new Schema([
    'query' => $queryType,
    'mutation' => $mutationType,
]);
```

---

## 8.2 The Type System

### Scalar Types

```php
<?php
use GraphQL\Type\Definition\Type;

Type::string();     // UTF-8 string
Type::int();        // Signed 32-bit integer
Type::float();      // Double precision float
Type::boolean();    // true/false
Type::id();         // Unique identifier (serialized as string)
```

### Custom Scalar Types

```php
<?php
use GraphQL\Type\Definition\CustomScalarType;

$dateType = new CustomScalarType([
    'name' => 'DateTime',
    'serialize' => fn(\DateTimeImmutable $value) => $value->format('c'),
    'parseValue' => fn(string $value) => new \DateTimeImmutable($value),
    'parseLiteral' => fn($valueNode) => new \DateTimeImmutable($valueNode->value),
]);
```

### Enum Types

```php
<?php
use GraphQL\Type\Definition\EnumType;

$postStatusType = new EnumType([
    'name' => 'PostStatus',
    'values' => [
        'DRAFT' => ['value' => 'draft'],
        'PUBLISHED' => ['value' => 'published'],
        'ARCHIVED' => ['value' => 'archived'],
    ],
]);
```

### Union and Interface Types

```php
<?php
// Interface
$nodeInterface = new InterfaceType([
    'name' => 'Node',
    'fields' => [
        'id' => Type::nonNull(Type::id()),
    ],
    'resolveType' => fn($value) => $value instanceof User ? $userType : $postType,
]);

// Union
$searchResultType = new UnionType([
    'name' => 'SearchResult',
    'types' => [$userType, $postType],
    'resolveType' => fn($value) => $value instanceof User ? $userType : $postType,
]);
```

---

## 8.3 Input Types and Mutations

### Input Types

```php
<?php
use GraphQL\Type\Definition\InputObjectType;

$createPostInput = new InputObjectType([
    'name' => 'CreatePostInput',
    'fields' => [
        'title' => Type::nonNull(Type::string()),
        'content' => Type::nonNull(Type::string()),
        'status' => Type::nonNull($postStatusType),
    ],
]);
```

### Mutation with Input

```php
<?php
$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'createPost' => [
            'type' => $postType,
            'args' => [
                'input' => Type::nonNull($createPostInput),
            ],
            'resolve' => fn($root, array $args, $context) => 
                $context->postService->create($args['input']),
        ],
    ],
]);
```

---

## 8.4 Solving the N+1 Problem with DataLoader

The N+1 problem occurs when a query like this:

```graphql
{
  users {
    posts { title }
  }
}
```

Generates N+1 SQL queries (1 for users + N for each user's posts).

### DataLoader Solution

```php
<?php
use App\DataLoaders\DataLoader;

class PostLoader
{
    private DataLoader $loader;

    public function __construct()
    {
        $this->loader = new DataLoader(function (array $userIds): array {
            // Single batched query
            $posts = Post::whereIn('user_id', $userIds)->get();
            
            // Group by user_id
            $grouped = [];
            foreach ($posts as $post) {
                $grouped[$post->user_id][] = $post;
            }
            
            // Return in same order as input
            return array_map(
                fn($id) => $grouped[$id] ?? [],
                $userIds
            );
        });
    }

    public function load(int $userId): array
    {
        return $this->loader->load($userId);
    }
}

// Usage in resolver
'posts' => [
    'type' => Type::listOf($postType),
    'resolve' => fn(User $user, $args, $context) => 
        $context->postLoader->load($user->id),
],
```

### Full DataLoader Implementation

```php
<?php
namespace App\DataLoaders;

class DataLoader
{
    private array $keys = [];
    private array $resolved = [];
    private array $promises = [];
    private \Closure $batchFn;
    private bool $pending = false;

    public function __construct(callable $batchFn)
    {
        $this->batchFn = \Closure::fromCallable($batchFn);
    }

    public function load($key): mixed
    {
        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }

        $this->keys[spl_object_id($this)][$key] = $key;

        if (!$this->pending) {
            $this->pending = true;
            register_shutdown_function([$this, 'dispatch']);
        }

        return new Promise(function ($resolve) use ($key) {
            $this->promises[$key][] = $resolve;
        });
    }

    public function dispatch(): void
    {
        if (empty($this->keys)) {
            return;
        }

        $keys = array_values($this->keys[spl_object_id($this)]);
        $this->keys = [];

        $result = ($this->batchFn)($keys);

        foreach ($keys as $index => $key) {
            $this->resolved[$key] = $result[$index] ?? null;
            
            if (isset($this->promises[$key])) {
                foreach ($this->promises[$key] as $resolve) {
                    $resolve($this->resolved[$key]);
                }
            }
        }

        $this->promises = [];
        $this->pending = false;
    }
}
```

---

## 8.5 Pagination with Connections

### Relay Connection Pattern

```php
<?php
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$postEdge = new ObjectType([
    'name' => 'PostEdge',
    'fields' => [
        'node' => $postType,
        'cursor' => Type::nonNull(Type::string()),
    ],
]);

$pageInfoType = new ObjectType([
    'name' => 'PageInfo',
    'fields' => [
        'hasNextPage' => Type::nonNull(Type::boolean()),
        'hasPreviousPage' => Type::nonNull(Type::boolean()),
        'startCursor' => Type::string(),
        'endCursor' => Type::string(),
    ],
]);

$postConnection = new ObjectType([
    'name' => 'PostConnection',
    'fields' => [
        'edges' => Type::listOf($postEdge),
        'pageInfo' => Type::nonNull($pageInfoType),
        'totalCount' => Type::int(),
    ],
]);

// Cursor-based pagination resolver
'posts' => [
    'type' => $postConnection,
    'args' => [
        'first' => Type::int(),
        'after' => Type::string(),
        'last' => Type::int(),
        'before' => Type::string(),
    ],
    'resolve' => function ($root, array $args) {
        $query = Post::query();
        
        if (isset($args['after'])) {
            $cursor = base64_decode($args['after']);
            $query->where('id', '>', $cursor);
        }
        
        if (isset($args['before'])) {
            $cursor = base64_decode($args['before']);
            $query->where('id', '<', $cursor);
        }
        
        $limit = $args['first'] ?? 10;
        $posts = $query->limit($limit + 1)->get();
        
        $hasNextPage = $posts->count() > $limit;
        $edges = $posts->take($limit)->map(fn($post) => [
            'node' => $post,
            'cursor' => base64_encode($post->id),
        ]);
        
        return [
            'edges' => $edges,
            'pageInfo' => [
                'hasNextPage' => $hasNextPage,
                'hasPreviousPage' => isset($args['after']),
                'startCursor' => $edges->first()['cursor'] ?? null,
                'endCursor' => $edges->last()['cursor'] ?? null,
            ],
            'totalCount' => Post::count(),
        ];
    },
],
```

---

## 8.6 Authentication and Authorization

### Context-Based Auth

```php
<?php
use GraphQL\GraphQL;

// Server setup with auth context
$context = [
    'user' => null,
    'isAuthenticated' => false,
];

// Parse JWT from Authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
    try {
        $payload = JWT::decode($matches[1], $secretKey, ['HS256']);
        $context['user'] = User::find($payload->sub);
        $context['isAuthenticated'] = true;
    } catch (\Exception $e) {
        // Invalid token
    }
}

// Field-level authorization
$adminQuery = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'adminStats' => [
            'type' => $statsType,
            'resolve' => function ($root, $args, $context) {
                if (!$context['isAuthenticated'] || $context['user']->role !== 'admin') {
                    throw new \RuntimeException('Unauthorized');
                }
                return getAdminStats();
            },
        ],
    ],
]);
```

---

## 8.7 Subscriptions (Real-Time)

```php
<?php
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$subscriptionType = new ObjectType([
    'name' => 'Subscription',
    'fields' => [
        'postCreated' => [
            'type' => $postType,
            'resolve' => fn($root) => $root,
        ],
    ],
]);

// Server-side publish (using a Redis pub/sub or similar)
class PostEventSubscriber
{
    public function publishPostCreated(Post $post): void
    {
        $payload = json_encode([
            'type' => 'post_created',
            'data' => $post->toArray(),
        ]);
        
        // Publish to Redis channel
        Redis::publish('graphql:events', $payload);
    }
}
```

---

## 8.8 GraphQL vs REST Decision Guide

| Aspect | GraphQL | REST |
|--------|---------|------|
| Data fetching | Client specifies exact fields | Server defines response shape |
| Over-fetching | None | Common |
| Under-fetching | None | Common (requires N+1 requests) |
| Caching | Complex (per-field) | Simple (per-URL) |
| File uploads | Complex | Simple (multipart) |
| Tooling | GraphiQL, Apollo DevTools | Postman, Swagger |
| Learning curve | Steeper | Gentle |
| Versioning | Evolve schema (no versioning) | URL/header versioning |
| Best for | Complex UIs, mobile apps | Simple CRUD, public APIs |
| Performance | Single endpoint, complex queries | Multiple endpoints, simple queries |

**When to choose GraphQL:**
- Complex, interconnected data models
- Multiple clients (web, mobile, IoT) with different data needs
- Rapidly evolving frontends
- Real-time features

**When to choose REST:**
- Simple CRUD operations
- Public APIs with broad consumer base
- Heavy caching requirements
- File upload/download heavy workflows

---

## 8.9 Exercises

1. Translate a REST API endpoint (GET /users/:id/posts) to a GraphQL query
2. Design a full schema for a blog with posts, comments, users, tags, and categories
3. Implement DataLoader to solve the N+1 problem for comments on posts
4. Add cursor-based pagination to a user's post list
5. Create a mutation with input type validation for user registration
6. Implement authentication middleware that rejects unauthenticated queries
7. Add a subscription that notifies clients when a new comment is added
8. Benchmark the same data fetch in REST vs GraphQL and compare payload sizes
9. Implement a custom scalar type for IP addresses with validation
10. Set up GraphiQL IDE for interactive schema exploration

---

## Further Reading

- **Doc:** [GraphQL PHP](https://webonyx.github.io/graphql-php/)
- **Doc:** [GraphQL Specification](https://graphql.org/learn/)
- **Doc:** [Relay Connection Spec](https://relay.dev/graphql/connections.htm)
- **Doc:** [DataLoader](https://github.com/facebook/dataloader)
- **Book:** "GraphQL in Action" by Samer Buna
- **Tool:** [GraphiQL](https://github.com/graphql/graphiql)
