# GraphQL vs REST — Decision Guide

## When to use REST

| Scenario | Why |
|----------|-----|
| Simple CRUD APIs | REST is straightforward, well-understood |
| Public APIs with stable contracts | REST is easier to cache, simpler for clients |
| File uploads | REST handles multipart natively |
| Caching is critical | HTTP caching (ETag, Cache-Control) works naturally |
| You need broad client compatibility | REST works with any HTTP client, no special library needed |
| Microservice-to-microservice | Simple, lightweight, low overhead |

## When to use GraphQL

| Scenario | Why |
|----------|-----|
| Complex, nested data requirements | One query fetches exactly what's needed |
| Multiple clients with different data needs | Each client specifies its own shape |
| Rapid frontend iteration | No backend changes for new data combinations |
| Mobile apps with limited bandwidth | Request only the fields you need |
| Dashboard / analytics UIs | Flexible aggregation without multiple round-trips |
| BFF (Backend For Frontend) pattern | Single GraphQL gateway aggregates multiple services |

## REST Example

### Request
```
GET /api/users?include=posts,comments
```

### Response
```json
{
    "data": [
        {
            "id": 1,
            "name": "Alice",
            "email": "alice@example.com",
            "posts": [
                {
                    "id": 10,
                    "title": "Hello World",
                    "comments": [
                        { "id": 100, "body": "Great post!" },
                        { "id": 101, "body": "Thanks!" }
                    ]
                }
            ]
        }
    ]
}
```

### Problems
- **Over-fetching:** Client gets entire user object when only `name` is needed
- **Under-fetching:** Multiple round-trips to get users + posts + comments
- **Versioning:** `/api/v2/users` adds API surface complexity
- **N+1 problem:** Each user triggers additional queries for posts and comments

## GraphQL Example

### Request
```graphql
query {
    users {
        name
        posts(limit: 5) {
            title
            comments {
                body
            }
        }
    }
}
```

### Response
```json
{
    "data": {
        "users": [
            {
                "name": "Alice",
                "posts": [
                    {
                        "title": "Hello World",
                        "comments": [
                            { "body": "Great post!" },
                            { "body": "Thanks!" }
                        ]
                    }
                ]
            }
        ]
    }
}
```

### Benefits
- **Exact data:** Name, title, body — nothing more, nothing less
- **Single endpoint:** `/graphql` handles everything
- **No versioning:** Add fields without breaking existing clients
- **Introspection:** Clients discover schema automatically

## Schema Comparison

### REST (OpenAPI)
```yaml
paths:
  /users:
    get:
      parameters:
        - name: include
          in: query
          schema:
            type: string
      responses:
        200:
          content:
            application/json:
              schema:
                type: array
                items:
                  $ref: '#/components/schemas/User'
  /users/{id}:
    get:
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: integer
      responses:
        200:
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/User'
```

### GraphQL (SDL)
```graphql
type User {
    id: ID!
    name: String!
    email: String!
    posts(limit: Int, offset: Int): [Post!]!
    createdAt: DateTime!
}

type Post {
    id: ID!
    title: String!
    body: String!
    comments: [Comment!]!
    author: User!
}

type Comment {
    id: ID!
    body: String!
    author: User!
}

type Query {
    users(page: Int, perPage: Int): [User!]!
    user(id: ID!): User
}

type Mutation {
    createUser(input: CreateUserInput!): User!
    updateUser(id: ID!, input: UpdateUserInput!): User!
    deleteUser(id: ID!): Boolean!
}
```

## Performance Considerations

| Aspect | REST | GraphQL |
|--------|------|---------|
| Caching | HTTP caching (CDN, browser) | Application-level caching (Redis) |
| Query parsing | None | Parsing + validation per request |
| Batching | Not built-in | DataLoader for batching + caching |
| File upload | Native multipart | Requires custom handling |
| Complexity | Simple URL routing | Potential expensive nested queries |

### N+1 Problem in GraphQL

```graphql
# This query looks innocent but creates N+1 queries
query {
    users {
        name
        posts { title }
    }
}
```

**Solution: DataLoader**

```php
// DataLoader batches + caches database queries
class DataLoader
{
    private array $loaders = [];

    public function __construct(private PDO $pdo) {}

    public function loader(string $name, callable $batchFn): callable
    {
        if (!isset($this->loaders[$name])) {
            $this->loaders[$name] = new class($batchFn) {
                private array $queue = [];
                private array $cache = [];
                private bool $scheduled = false;

                public function __construct(private callable $batchFn) {}

                public function load(mixed $key): mixed
                {
                    if (isset($this->cache[$key])) {
                        return $this->cache[$key];
                    }

                    $this->queue[] = $key;

                    if (!$this->scheduled) {
                        $this->scheduled = true;
                        register_shutdown_function(function () {
                            $this->flush();
                        });
                    }

                    // For immediate resolution, you'd use deferred resolution
                    // This is a simplified example
                    return null;
                }

                public function flush(): void
                {
                    if (empty($this->queue)) return;

                    $results = ($this->batchFn)(array_unique($this->queue));
                    foreach ($results as $key => $value) {
                        $this->cache[$key] = $value;
                    }
                    $this->queue = [];
                }
            };
        }

        return fn(mixed $key) => $this->loaders[$name]->load($key);
    }
}
```

## Implementation: GraphQL in PHP (with webonyx/graphql-php)

```bash
composer require webonyx/graphql-php
```

```php
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

// Define types
$userType = new ObjectType([
    'name' => 'User',
    'fields' => function () use (&$postType) {
        return [
            'id' => Type::nonNull(Type::id()),
            'name' => Type::string(),
            'email' => Type::string(),
            'posts' => [
                'type' => Type::listOf($postType),
                'args' => [
                    'limit' => Type::int(),
                ],
                'resolve' => function (array $user, array $args) {
                    global $pdo;
                    $stmt = $pdo->prepare(
                        'SELECT * FROM posts WHERE user_id = ? LIMIT ?'
                    );
                    $stmt->execute([$user['id'], $args['limit'] ?? 10]);
                    return $stmt->fetchAll();
                },
            ],
        ];
    },
]);

$postType = new ObjectType([
    'name' => 'Post',
    'fields' => [
        'id' => Type::nonNull(Type::id()),
        'title' => Type::string(),
        'body' => Type::string(),
    ],
]);

// Query type
$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'users' => [
            'type' => Type::listOf($userType),
            'resolve' => function () {
                global $pdo;
                return $pdo->query('SELECT * FROM users')->fetchAll();
            },
        ],
        'user' => [
            'type' => $userType,
            'args' => [
                'id' => Type::nonNull(Type::id()),
            ],
            'resolve' => function ($root, array $args) {
                global $pdo;
                $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
                $stmt->execute([$args['id']]);
                return $stmt->fetch();
            },
        ],
    ],
]);

// Schema
$schema = new Schema(['query' => $queryType]);

// Execute
$input = json_decode(file_get_contents('php://input'), true);
$result = GraphQL::executeQuery($schema, $input['query']);
echo json_encode($result->toArray());
```

## Hybrid Approach

Best of both worlds for large applications:
- **REST for simple CRUD** — users, posts, comments
- **GraphQL for complex queries** — dashboards, reports, mobile apps
- **GraphQL as BFF** — single GraphQL gateway aggregating multiple REST microservices

```php
// GraphQL resolver that delegates to REST
'resolve' => function () {
    $users = json_decode(
        file_get_contents('http://users-service/api/users'),
        true
    );
    $orders = json_decode(
        file_get_contents('http://orders-service/api/orders/batch', ...),
        true
    );
    // Merge and return
    return array_map(fn($u) => [
        ...$u,
        'orders' => $orders[$u['id']] ?? []
    ], $users);
};
```
