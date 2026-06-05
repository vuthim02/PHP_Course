# Composer — Deep Reference

## Installation

```bash
# Install globally
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version
composer --help

# Self-update
composer self-update
composer self-update --rollback
```

## Basic Commands

```bash
# Create a new project
composer create-project laravel/laravel my-app
composer create-project symfony/skeleton my-app

# Initialize a new package
composer init
composer init --name="vendor/package" --description="..." --type=library

# Require a package (install + add to composer.json)
composer require vendor/package
composer require vendor/package:^2.0
composer require vendor/package:dev-main#abc123  # Specific commit
composer require vendor/package --dev             # Dev dependency

# Install from composer.lock (exact versions)
composer install

# Install ignoring lock file (update all to latest within constraints)
composer update

# Update a specific package
composer update vendor/package

# Remove a package
composer remove vendor/package
```

## Version Constraints

```json
{
    "require": {
        "php": "8.1.*",
        "vendor/package": "^2.0",
        "another/package": "~1.5",
        "exact/package": "1.2.3",
        "range/package": ">=1.0 <3.0",
        "patch/package": ">=1.0,<2.0||>=3.0"
    }
}
```

| Constraint | Meaning |
|-----------|---------|
| `^1.2.3` | >=1.2.3, <2.0.0 (compatible with minor bumps) |
| `~1.2.3` | >=1.2.3, <1.3.0 (only patch bumps) |
| `1.2.*` | >=1.2.0, <1.3.0 |
| `>=1.0` | At least version 1.0 |
| `<2.0` | Below version 2.0 |
| `1.0 - 2.0` | Inclusive range |
| `dev-main` | Development branch (installs as source) |
| `*` | Any version |
| `@dev` | Allow unstable releases |

### Caret vs Tilde

```
^1.2.3  →  1.2.3 ≤ x < 2.0.0  (safe for semver)
^0.3.0  →  0.3.0 ≤ x < 0.4.0  (respects 0.x instability)
~1.2.3  →  1.2.3 ≤ x < 1.3.0  (only patch)
~1.2    →  1.2.0 ≤ x < 2.0.0  (minor and patch)
```

## Composer.json — All Sections

```json
{
    "name": "vendor/package",
    "description": "What this package does",
    "type": "library",
    "keywords": ["php", "framework"],
    "homepage": "https://github.com/vendor/package",
    "license": "MIT",

    "authors": [
        {
            "name": "Author Name",
            "email": "author@example.com",
            "homepage": "https://example.com",
            "role": "Developer"
        }
    ],

    "require": {
        "php": ">=8.1",
        "ext-pdo": "*",
        "ext-json": "*",
        "vendor/package": "^1.0"
    },

    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^1.10"
    },

    "conflict": {
        "incompatible/package": ">=2.0"
    },

    "replace": {
        "legacy/package": "self.version"
    },

    "provide": {
        "psr/container-implementation": "1.0"
    },

    "suggest": {
        "monolog/monolog": "Advanced logging"
    },

    "autoload": {
        "psr-4": {
            "Vendor\\Package\\": "src/"
        },
        "classmap": [
            "src/Commands/",
            "src/Legacy/"
        ],
        "files": [
            "src/functions.php",
            "src/helpers.php"
        ]
    },

    "autoload-dev": {
        "psr-4": {
            "Vendor\\Package\\Tests\\": "tests/"
        }
    },

    "minimum-stability": "stable",
    "prefer-stable": true,

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/private-package"
        },
        {
            "type": "path",
            "url": "../local-package",
            "options": { "symlink": true }
        }
    ],

    "scripts": {
        "pre-install-cmd": [
            "@php -r \"echo 'Installing...';\""
        ],
        "post-install-cmd": [
            "php artisan migrate"
        ],
        "test": "phpunit",
        "cs-fix": "php-cs-fixer fix src/",
        "analyse": "phpstan analyse src/ --level=max"
    },

    "extra": {
        "branch-alias": {
            "dev-main": "1.x-dev"
        },
        "laravel": {
            "providers": [
                "Vendor\\Package\\ServiceProvider"
            ]
        }
    },

    "config": {
        "sort-packages": true,
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "process-timeout": 300,
        "allow-plugins": {
            "composer/installers": true,
            "phpstan/extension-installer": true
        }
    }
}
```

## Package Types

| Type | Purpose |
|------|---------|
| `library` | Reusable PHP library (default) |
| `project` | Application skeleton (laravel/laravel) |
| `composer-plugin` | Composer plugin |
| `metapackage` | Collection of dependencies, no files |
| `php-ext` | PHP extension |

## Autoloading — PSR-4

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Maps `App\Controller\UserController` → `src/Controller/UserController.php`

### Autoloading Optimization

```bash
# Development — dynamic, supports class generation
composer dump-autoload

# Production — optimized classmap
composer dump-autoload --optimize
# Or:
composer install --optimize-autoloader

# Authoritative — no filesystem checks
composer dump-autoload --classmap-authoritative

# APCu cache
composer dump-autoload --apcu
```

## Repository Types

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.org"
        },
        {
            "type": "vcs",
            "url": "https://github.com/vendor/package"
        },
        {
            "type": "path",
            "url": "../local/packages/*",
            "options": {
                "symlink": true,
                "reference": "none"
            }
        },
        {
            "type": "artifact",
            "url": "path/to/directory/with/zips/"
        }
    ]
}
```

## Scripts & Hooks

```json
{
    "scripts": {
        "pre-install-cmd": [],
        "post-install-cmd": [],
        "pre-update-cmd": [],
        "post-update-cmd": [],
        "pre-autoload-dump": [],
        "post-autoload-dump": [],
        "post-root-package-install": [],
        "post-create-project-cmd": []
    }
}
```

```bash
# Run custom scripts
composer run-script test
composer run-script cs-fix

# List all available scripts
composer list scripts
```

## Common Patterns

### Private Packages with Satis
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://satis.example.com"
        }
    ],
    "require": {
        "vendor/private-package": "^1.0"
    }
}
```

### Monorepo with Path Repository
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/*",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "my/monorepo-package-a": "*",
        "my/monorepo-package-b": "*"
    }
}
```

### Platform Packages
```json
{
    "config": {
        "platform": {
            "php": "8.1.12",
            "ext-pdo": "1.0",
            "ext-redis": "5.3"
        }
    }
}
```

## Troubleshooting

```bash
# Debug autoloading
composer dump-autoload -o

# Check why a version resolved the way it did
composer why vendor/package
composer why-not vendor/package 2.0.0

# Show installed packages
composer show
composer show vendor/package

# Show outdated packages
composer outdated

# Audit for security vulnerabilities
composer audit

# Diagnose issues
composer diagnose

# Clear cache
composer clear-cache
```

## Composer.lock

```bash
# The lock file pins exact versions. ALWAYS commit it.

# First install — creates composer.lock
composer install

# Subsequent installs — uses exact versions from lock
composer install

# To update to latest within version constraints
composer update

# To update only one package
composer update vendor/package

# To update to latest ignoring constraints (dangerous)
composer update --with-all-dependencies

# Check if lock is synchronized with json
composer validate
```

## Best Practices

1. **Commit `composer.lock`** for applications, NOT for libraries
2. **Use `^` constraints** (caret) by default
3. **Lock PHP version** in `config.platform`
4. **Run `composer audit`** in CI
5. **Optimize autoloader** in production deployments
6. **Never run `composer update` in production** — always use `composer install`
7. **Use `--no-dev` in production**: `composer install --no-dev --optimize-autoloader`
8. **Sort packages** with `"sort-packages": true`
9. **Use semantic versioning** for your packages
10. **Test on both `prefer-lowest` and `prefer-stable`** in CI:
    ```bash
    composer update --prefer-lowest --prefer-stable
    ```
