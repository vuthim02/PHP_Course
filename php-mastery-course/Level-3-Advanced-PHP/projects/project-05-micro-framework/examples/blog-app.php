<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MicroFramework\Http\Request;
use MicroFramework\Http\Response;
use MicroFramework\Router;
use MicroFramework\Container\Container;

echo "=== PHP Micro-Framework Demo ===\n\n";

// --- Dependency Injection Container ---
$container = new Container();

// Register services
$container->setSingleton('db', function () {
    return new class {
        private array $posts = [
            1 => ['id' => 1, 'title' => 'Getting Started with PHP', 'body' => 'PHP is a popular scripting language.', 'author' => 'Alice'],
            2 => ['id' => 2, 'title' => 'Advanced OOP', 'body' => 'Learn about inheritance, interfaces, and traits.', 'author' => 'Bob'],
            3 => ['id' => 3, 'title' => 'PHP 8 Features', 'body' => 'Attributes, named arguments, JIT, and more.', 'author' => 'Charlie'],
        ];
        private int $nextId = 4;

        public function getAll(): array
        {
            return array_values($this->posts);
        }

        public function getById(int $id): ?array
        {
            return $this->posts[$id] ?? null;
        }

        public function create(string $title, string $body, string $author): array
        {
            $id = $this->nextId++;
            $this->posts[$id] = ['id' => $id, 'title' => $title, 'body' => $body, 'author' => $author];
            return $this->posts[$id];
        }

        public function delete(int $id): bool
        {
            if (isset($this->posts[$id])) {
                unset($this->posts[$id]);
                return true;
            }
            return false;
        }
    };
});

// --- Router ---
$router = new Router();

// Named routes
$router->get('/', function (Request $request): string {
    return '<h1>PHP Micro-Framework</h1>
            <p>Welcome! Visit <a href="/posts">/posts</a> for the blog.</p>';
})->name('home');

$router->get('/hello/{name}', function (Request $request, string $name): Response {
    return Response::json(['message' => "Hello, $name!", 'path' => $request->getPath()]);
})->where('name', '[a-zA-Z]+');

// Resourceful routes (blog)
$router->get('/posts', function (Container $c): Response {
    $db = $c->get('db');
    $posts = $db->getAll();
    $html = '<h1>Blog Posts</h1><ul>';
    foreach ($posts as $post) {
        $html .= "<li><a href='/posts/{$post['id']}'>{$post['title']}</a> by {$post['author']}</li>";
    }
    $html .= '</ul><p><a href="/posts/new">Create New Post</a></p>';
    return new Response(200, $html);
})->name('posts.index');

$router->get('/posts/{id}', function (Container $c, int $id): Response {
    $db = $c->get('db');
    $post = $db->getById($id);
    if ($post === null) {
        return Response::json(['error' => 'Post not found'], 404);
    }
    $html = "<h1>{$post['title']}</h1>
             <p><em>by {$post['author']}</em></p>
             <p>{$post['body']}</p>
             <p><a href='/posts'>Back to posts</a></p>";
    return new Response(200, $html);
})->where('id', '\d+')->name('posts.show');

$router->get('/posts/new', function (): string {
    return '<h1>New Post</h1>
            <form method="POST" action="/posts">
                <input name="title" placeholder="Title"><br>
                <textarea name="body" placeholder="Body"></textarea><br>
                <input name="author" placeholder="Author"><br>
                <button type="submit">Create</button>
            </form>
            <p><a href="/posts">Back</a></p>';
})->name('posts.new');

$router->post('/posts', function (Container $c, Request $request): Response {
    $data  = $request->getParsedBody();
    $title = $data['title'] ?? 'Untitled';
    $body  = $data['body'] ?? '';
    $author = $data['author'] ?? 'Anonymous';

    $db = $c->get('db');
    $post = $db->create($title, $body, $author);

    return Response::redirect("/posts/{$post['id']}", 201);
})->name('posts.store');

$router->delete('/posts/{id}', function (Container $c, int $id): Response {
    $db = $c->get('db');
    $db->delete($id);
    return Response::json(['deleted' => true, 'id' => $id]);
})->where('id', '\d+')->name('posts.destroy');

// Router group with prefix and middleware
$router->group('api', function (Router $router): void {
    $router->get('/status', function (): Response {
        return Response::json(['status' => 'ok', 'version' => '1.0']);
    })->name('api.status');

    $router->get('/stats', function (Container $c): Response {
        $db = $c->get('db');
        return Response::json([
            'total_posts' => count($db->getAll()),
        ]);
    })->name('api.stats');
});

// Global middleware (logging)
$router->addGlobalMiddleware(function (Request $request, callable $next): Response {
    $start = microtime(true);
    $response = $next($request);
    $elapsed = (microtime(true) - $start) * 1000;
    echo "[middleware] {$request->getMethod()} {$request->getPath()} → {$response->getStatusCode()} ({$elapsed}ms)\n";
    return $response;
});

// Add middleware (auth simulation)
$router->addGlobalMiddleware(function (Request $request, callable $next): Response {
    if ($request->getPath() === '/admin' && $request->getHeader('X-Auth') !== 'secret') {
        return Response::json(['error' => 'Unauthorized'], 401);
    }
    return $next($request);
});

// --- Simulate Requests ---
$simulatedRequests = [
    Request::fromGlobals()->withMethod('GET')->withMethod('GET'),
    null, null, null, null, null,
];

// Override with simulated data
$simulatedRequests = [
    ['GET', '/'],
    ['GET', '/hello/PHP8'],
    ['GET', '/posts'],
    ['GET', '/posts/2'],
    ['GET', '/posts/new'],
    ['POST', '/posts', ['title' => 'New Post', 'body' => 'Post body content', 'author' => 'TestUser']],
    ['GET', '/posts/4'],
    ['DELETE', '/posts/1'],
    ['GET', '/posts'],
    ['GET', '/api/status'],
    ['GET', '/api/stats'],
    ['GET', '/nonexistent'],
];

foreach ($simulatedRequests as $i => $sim) {
    $method = $sim[0];
    $uri    = $sim[1];
    $postData = $sim[2] ?? [];

    $server = [
        'REQUEST_METHOD' => $method,
        'REQUEST_URI'    => $uri,
    ];

    // Build request manually for simulation
    $request = new Request(
        method:  $method,
        uri:     $uri,
        headers: $method === 'DELETE' ? [] : ($method === 'POST' ? ['Content-Type' => 'application/x-www-form-urlencoded'] : []),
        query:   [],
        post:    $postData,
        server:  $server,
    );

    echo "\n--- Request #" . ($i + 1) . ": $method $uri ---\n";

    try {
        $response = $router->dispatch($request);
        echo "Status: {$response->getStatusCode()}\n";
        echo "Body:\n{$response->getBody()}\n";
    } catch (\Throwable $e) {
        echo "Error: {$e->getMessage()}\n";
    }
}

echo "\n\n--- Named Routes ---\n";
echo "Home URL: " . $router->url('home') . "\n";
echo "Post #5 URL: " . $router->url('posts.show', ['id' => 5]) . "\n";
echo "API status URL: " . $router->url('api.status') . "\n";

echo "\nDemo completed successfully!\n";
