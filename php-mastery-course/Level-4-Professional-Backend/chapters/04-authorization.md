# Chapter 4: Authorization

## Learning Objectives

- Implement role-based access control (RBAC)
- Create permission policies
- Build middleware for authorization
- Apply resource-level access control

---

## 4.1 Policy-Based Authorization

```php
<?php
namespace App\Auth;

abstract class Policy
{
    protected ?User $user = null;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    abstract public function before(User $user, string $ability): ?bool;

    protected function owns(Model $model, string $column = 'user_id'): bool
    {
        return $this->user?->id === $model->$column;
    }
}

class PostPolicy extends Policy
{
    public function before(User $user, string $ability): ?bool
    {
        // Admin can do everything
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    public function view(?User $user, Post $post): bool
    {
        return $post->status === 'published' || ($user && $this->owns($post));
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'author']);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->owns($post) || $user->role === 'editor';
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->role === 'admin' || $this->owns($post);
    }

    public function publish(User $user, Post $post): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }
}

class Gate
{
    private array $policies = [];
    private array $abilities = [];
    private ?User $user = null;

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function policy(string $modelClass, string $policyClass): void
    {
        $this->policies[$modelClass] = $policyClass;
    }

    public function define(string $ability, callable $callback): void
    {
        $this->abilities[$ability] = $callback;
    }

    public function allows(string $ability, mixed $arguments = []): bool
    {
        if (!$this->user) {
            return false;
        }

        // Check defined abilities
        if (isset($this->abilities[$ability])) {
            return (bool)($this->abilities[$ability])($this->user, ...(array)$arguments);
        }

        // Check policy
        if (is_object($arguments)) {
            $modelClass = get_class($arguments);
            if (isset($this->policies[$modelClass])) {
                $policy = new $this->policies[$modelClass]();
                $policy->setUser($this->user);
                
                // Check before hook
                $before = $policy->before($this->user, $ability);
                if ($before !== null) {
                    return $before;
                }

                if (method_exists($policy, $ability)) {
                    return $policy->$ability($this->user, $arguments);
                }
            }
        }

        return false;
    }

    public function authorize(string $ability, mixed $arguments = []): void
    {
        if (!$this->allows($ability, $arguments)) {
            throw new AuthorizationException("Unauthorized: {$ability}");
        }
    }
}

// Middleware
class AuthorizeMiddleware
{
    public function __construct(private Gate $gate) {}

    public function handle(string $ability, string $paramName = 'id'): callable
    {
        return function (callable $next) use ($ability, $paramName) {
            $this->gate->setUser($_REQUEST['auth_user'] ?? null);
            
            // Find model from route parameter
            $modelId = $_REQUEST[$paramName] ?? null;
            $model = $modelId ? $this->findModel($ability, $modelId) : null;
            
            $this->gate->authorize($ability, $model);
            $next();
        };
    }
}

// Usage in controller
class PostController
{
    public function update(int $id): never
    {
        $post = Post::findOrFail($id);
        
        try {
            Gate::authorize('update', $post);
            // Update post...
            ApiResponse::success($post);
        } catch (AuthorizationException $e) {
            ApiResponse::error($e->getMessage(), 403);
        }
    }
}
```

---

## 4.2 Exercises

1. Create policies for User, Comment, and Category models
2. Define abilities for admin dashboard access
3. Implement middleware that checks permissions before route handlers
4. Add team-based authorization (user belongs to teams with roles)

---

## Further Reading

- **Doc:** [Laravel Authorization](https://laravel.com/docs/11.x/authorization)
