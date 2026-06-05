# Chapter 16: Namespaces and Autoloading

## Learning Objectives

- Organize code with namespaces
- Understand use statements and aliases
- Configure autoloading for projects

---

## 16.1 Namespace Organization

```php
<?php
// File: src/Http/Controllers/Api/UserController.php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(): UserResource
    {
        $users = User::paginate();
        return new UserResource($users);
    }
}

// File: src/Models/User.php
namespace App\Models;

use App\Traits\HasTimestamps;

class User
{
    use HasTimestamps;

    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}
}

// File: src/Services/Payment/Providers/StripeProvider.php
namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PaymentProviderInterface;

class StripeProvider implements PaymentProviderInterface
{
    // ...
}
```

---

## 16.2 Importing and Aliasing

```php
<?php
// Full import
use App\Models\User;

// Import with alias (for conflicts)
use App\Models\Post as BlogPost;
use App\Legacy\Models\Post as LegacyPost;

// Import multiple from same namespace
use App\Http\Controllers\{
    UserController,
    PostController,
    CommentController,
};

// Import function
use function array_flatten;
use function App\Helpers\formatCurrency;

// Import constant
use const App\Config\MAX_UPLOAD_SIZE;

// Fully qualified (no import needed)
$user = new \App\Models\User();
```

---

## 16.3 Exercises

1. Organize a project into meaningful namespaces
2. Resolve a naming conflict between two packages using aliases
3. Configure PSR-4 autoloading for your namespace structure

---

## Further Reading

- **Doc:** [PHP Namespaces](https://www.php.net/manual/en/language.namespaces.php)
