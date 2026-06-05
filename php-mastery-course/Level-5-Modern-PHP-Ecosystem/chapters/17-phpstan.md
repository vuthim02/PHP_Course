# Chapter 17: Static Analysis with PHPStan

## Learning Objectives

- Configure PHPStan at maximum level
- Write type-safe code
- Create custom PHPStan rules
- Integrate into CI/CD

---

## 17.1 PHPStan Configuration

```php
<?php
// phpstan.neon
parameters:
    level: max
    paths:
        - src/
    scanFiles:
        - vendor/autoload.php
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: false

    ignoreErrors:
        - '#Access to an undefined property#'
        - '#Method .* has no return type specified#'

    excludePaths:
        - src/Migrations/
        - src/Legacy/

// Level 0: Basic checks
// Level 1: Unknown class checks
// Level 2: Unknown method checks
// Level 3: Unknown property checks
// Level 4: Dead code detection
// Level 5: Type checking
// Level 6: Report missing return types
// Level 7: Report partial unions
// Level 8: Report mixed types
// Level 9: Strict type checking
// Level max: Strictest checks

// Example violations and fixes:

// ❌ Level 1: Unknown class
$user = new NonExistentClass();

// ❌ Level 5: Wrong type passed
function processUser(User $user): void {}
processUser('not-a-user'); // Error

// ❌ Level 6: Missing return type
function getUsers() { // Error: Missing return type
    return User::all();
}

// ✅ Fixed
function getUsers(): Collection
{
    return User::all();
}

// ❌ Level 8: Mixed type
function process(mixed $data): void
{
    echo $data->name; // Can't access property on mixed
}

// ✅ Fixed with type guard
function process(mixed $data): void
{
    if ($data instanceof User) {
        echo $data->name;
    }
}
```

---

## 17.2 Exercises

1. Configure PHPStan at maximum level for a project
2. Fix all errors until level max passes
3. Create a custom PHPStan rule for your project conventions
4. Integrate PHPStan into CI pipeline

---

## Further Reading

- **Doc:** [PHPStan](https://phpstan.org/)
- **Doc:** [PHPStan Rules](https://phpstan.org/user-guide/rule-levels)
