# Chapter 23: Authorization (RBAC)

## Learning Objectives

- Implement role-based access control
- Create permission middleware
- Design user roles and permissions
- Apply authorization checks

---

## 23.1 RBAC Implementation

```php
<?php
enum Role: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case AUTHOR = 'author';
    case SUBSCRIBER = 'subscriber';

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => Permission::all(),
            self::EDITOR => [
                Permission::VIEW_POSTS,
                Permission::CREATE_POSTS,
                Permission::EDIT_POSTS,
                Permission::PUBLISH_POSTS,
                Permission::VIEW_COMMENTS,
                Permission::MODERATE_COMMENTS,
            ],
            self::AUTHOR => [
                Permission::VIEW_POSTS,
                Permission::CREATE_POSTS,
                Permission::EDIT_OWN_POSTS,
            ],
            self::SUBSCRIBER => [
                Permission::VIEW_POSTS,
                Permission::CREATE_COMMENTS,
            ],
        };
    }
}

enum Permission: string
{
    case VIEW_POSTS = 'posts.view';
    case CREATE_POSTS = 'posts.create';
    case EDIT_POSTS = 'posts.edit';
    case EDIT_OWN_POSTS = 'posts.edit_own';
    case DELETE_POSTS = 'posts.delete';
    case PUBLISH_POSTS = 'posts.publish';
    case VIEW_USERS = 'users.view';
    case CREATE_USERS = 'users.create';
    case EDIT_USERS = 'users.edit';
    case DELETE_USERS = 'users.delete';
    case VIEW_COMMENTS = 'comments.view';
    case MODERATE_COMMENTS = 'comments.moderate';
    case CREATE_COMMENTS = 'comments.create';
    case VIEW_SETTINGS = 'settings.view';
    case EDIT_SETTINGS = 'settings.edit';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```

---

## 23.2 Authorization Service

```php
<?php
class AuthorizationService
{
    public function __construct(
        private ?\App\Models\User $user = null
    ) {}

    public function setUser(\App\Models\User $user): void
    {
        $this->user = $user;
    }

    public function can(Permission $permission): bool
    {
        if (!$this->user) {
            return false;
        }

        // Admin can do everything
        if ($this->user->role === Role::ADMIN) {
            return true;
        }

        // Check user's role permissions
        return in_array(
            $permission->value,
            $this->user->role->permissions()
        );
    }

    public function canAny(Permission ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    public function canAll(Permission ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($permission)) {
                return false;
            }
        }
        return true;
    }

    public function owns(int $resourceId, string $userIdColumn = 'user_id'): bool
    {
        if (!$this->user) {
            return false;
        }
        return $resourceId === $this->user->id;
    }

    public function authorize(Permission $permission): void
    {
        if (!$this->can($permission)) {
            throw new AuthorizationException(
                "Missing permission: {$permission->value}"
            );
        }
    }
}

// Authorization Middleware
class AuthorizationMiddleware
{
    public function __construct(
        private AuthorizationService $auth
    ) {}

    public function require(Permission $permission): callable
    {
        return function (callable $next) use ($permission) {
            $this->auth->authorize($permission);
            return $next();
        };
    }

    public function requireOwnership(int $resourceId): callable
    {
        return function (callable $next) use ($resourceId) {
            if (!$this->auth->owns($resourceId)) {
                throw new AuthorizationException('You do not own this resource');
            }
            return $next();
        };
    }
}
```

---

## 23.3 Exercises

1. Design roles and permissions for a project management system
2. Implement authorization checks in a controller
3. Create middleware that blocks unauthorized access
4. Add ownership checks for editing/deleting resources

---

## Further Reading

- **Doc:** [OWASP Authorization Cheatsheet](https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html)
