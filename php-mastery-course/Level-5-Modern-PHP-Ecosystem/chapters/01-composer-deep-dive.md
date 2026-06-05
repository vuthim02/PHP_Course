# Chapter 1: Composer Deep Dive

## Learning Objectives

- Create and publish Composer packages
- Understand semantic versioning and version constraints
- Use Composer scripts, hooks, and events
- Manage dependencies professionally
- Optimize autoloading for production
- Troubleshoot common Composer issues
- Implement private package repositories

---

## 1.1 Creating a Package

### Package Structure

```
payment-processor/
├── src/
│   └── PaymentProcessor.php
├── tests/
│   └── PaymentProcessorTest.php
├── composer.json
├── phpstan.neon
├── phpunit.xml
├── .gitignore
└── README.md
```

### composer.json Breakdown

```json
{
    "name": "vendor/payment-processor",
    "description": "Secure payment processing library",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "guzzlehttp/guzzle": "^7.0",
        "ramsey/uuid": "^4.7"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.10"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\PaymentProcessor\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Vendor\\PaymentProcessor\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "analyse": "phpstan analyse src/ --level=max",
        "check": ["@test", "@analyse"]
    },
    "config": {
        "sort-packages": true,
        "optimize-autoloader": true,
        "allow-plugins": {
            "php-http/discovery": true
        }
    },
    "extra": {
        "branch-alias": {
            "dev-main": "1.0.x-dev"
        }
    }
}
```

### Key composer.json Fields

| Field | Purpose | Example |
|-------|---------|---------|
| `name` | Vendor + package identifier | `vendor/payment-processor` |
| `type` | Package category | `library`, `project`, `composer-plugin` |
| `require` | Production dependencies with version constraints | `"php": "^8.2"` |
| `require-dev` | Development-only dependencies | `"phpunit/phpunit": "^10.0"` |
| `autoload` | PSR-4/PSR-0/classmap/files autoloading | `"psr-4": {"App\\": "src/"}` |
| `scripts` | Command hooks | `"post-update-cmd": "..."` |
| `config` | Composer behavior settings | `"optimize-autoloader": true` |
| `extra` | Arbitrary metadata for plugins/infra | `"branch-alias": {...}` |
| `minimum-stability` | Minimum package stability | `"stable"`, `"dev"` |

### Publishing to Packagist

```bash
# 1. Ensure your package has a valid composer.json
# 2. Push to GitHub
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/vendor/payment-processor
git tag v1.0.0
git push origin main --tags

# 3. Submit to Packagist
# Visit https://packagist.org/packages/submit
# Enter your GitHub URL
# Packagist will auto-update via GitHub webhook

# 4. Or use the CLI
composer global require namshi/composer-publish
composer publish
```

### Source vs Dist

```json
{
    "dist": {
        "type": "zip",
        "url": "https://api.github.com/repos/vendor/package/zipball/v1.0.0"
    },
    "source": {
        "type": "git",
        "url": "https://github.com/vendor/package.git",
        "reference": "abc123..."
    }
}
```

- **dist**: Archived zip — faster install, no VCS history. Preferred for stable releases
- **source**: Git clone — needed if you want to make changes to the dependency. Used for dev branches

Use `--prefer-dist` (default) or `--prefer-source` to control behavior.

---

## 1.2 Semantic Versioning

### Version Schema

```text
MAJOR.MINOR.PATCH

1.4.2 → Major: 1, Minor: 4, Patch: 2

Patch (1.4.2 → 1.4.3): Bug fixes, backward compatible
Minor (1.4.2 → 1.5.0): New features, backward compatible
Major (1.4.2 → 2.0.0): Breaking changes
```

### Pre-release Tags

```text
1.0.0-alpha.1    — Internal testing
1.0.0-beta.2     — Feature-complete, testing
1.0.0-rc.3       — Release candidate
1.0.0            — Stable release
1.0.1            — Patch
```

### Version Constraints in Detail

| Constraint | Meaning | Example Matches |
|------------|---------|-----------------|
| `^1.4.2` | >=1.4.2, <2.0.0 | 1.4.2, 1.5.0, 1.99.99 |
| `^0.3.2` | >=0.3.2, <0.4.0 (0.x is special) | 0.3.2, 0.3.99 |
| `~1.4.2` | >=1.4.2, <1.5.0 | 1.4.2, 1.4.99 |
| `~1.4` | >=1.4.0, <2.0.0 | 1.4.0, 1.9.99 |
| `1.4.*` | 1.4.x wildcard | 1.4.0, 1.4.1, 1.4.99 |
| `>=1.4` | 1.4 or higher | 1.4.0, 2.0.0 |
| `>=1.4, <2.0` | Range | 1.4.0, 1.9.99 |
| `dev-main` | Development branch | Latest commit on main |
| `dev-feature#abc123` | Branch + commit hash | Specific commit |

### Lock File

```json
{
    "packages": [
        {
            "name": "guzzlehttp/guzzle",
            "version": "7.8.0",
            "source": {
                "url": "https://github.com/guzzle/guzzle.git",
                "reference": "a8a2e2b..."
            },
            "dist": {
                "url": "https://api.github.com/repos/guzzle/guzzle/zipball/a8a2e2b..."
            }
        }
    ]
}
```

- `composer.lock` pins every dependency to exact versions
- **Always commit** `composer.lock` for applications (never for libraries)
- Use `composer install` for consistent installs across environments
- Use `composer update` to upgrade within constraints

---

## 1.3 Autoloading Optimization

### PSR-4 vs Classmap vs Files

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        },
        "classmap": [
            "src/legacy/",
            "src/LegacyClass.php"
        ],
        "files": [
            "src/helpers.php"
        ]
    }
}
```

| Type | Use Case | Performance |
|------|----------|-------------|
| PSR-4 | Modern namespaced code | Good (lazy-loaded) |
| Classmap | Legacy code, non-namespaced | Best (explicit list) |
| Files | Functions, global helpers | Loaded on every request |

### Production Optimization

```bash
# Generate optimized classmap (includes all classes as explicit paths)
composer dump-autoload -o

# Generate authoritative classmap (no filesystem checks at runtime)
composer dump-autoload -a

# APCu autoloader cache (if APCu extension is installed)
composer dump-autoload --apcu
```

Benchmark impact:

| Mode | Filesystem Stats | Memory | Speed |
|------|-----------------|--------|-------|
| Default | Yes | Low | Slow |
| Optimized (-o) | No classmap classes | Medium | Fast |
| Authoritative (-a) | None at all | Medium | Fastest |
| APCu | No repeated resolution | Low | Fastest |

---

## 1.4 Scripts and Hooks

### Available Events

| Event | Trigger | Common Use |
|-------|---------|------------|
| `pre-install-cmd` | Before `composer install` | Check requirements |
| `post-install-cmd` | After `composer install` | Clear cache, create dirs |
| `pre-update-cmd` | Before `composer update` | Backup lock file |
| `post-update-cmd` | After `composer update` | Run migrations |
| `pre-autoload-dump` | Before dumping autoload | Generate metadata |
| `post-autoload-dump` | After dumping autoload | Warm cache |
| `post-root-package-install` | After root package install | Create .env |
| `post-create-project-cmd` | After `composer create-project` | Run setup wizard |

### Real-World Scripts

```json
{
    "scripts": {
        "test": "phpunit --colors=always",
        "test-coverage": "phpunit --coverage-html coverage/",
        "analyse": "phpstan analyse src/ --level=max",
        "cs-fix": "php-cs-fixer fix src/",
        "cs-check": "php-cs-fixer fix src/ --dry-run --diff",
        "refactor": "rector process src/",
        "check": [
            "@cs-check",
            "@analyse",
            "@test"
        ],
        "fix": [
            "@cs-fix",
            "@refactor"
        ],
        "ci": [
            "@composer validate --strict",
            "@check"
        ],
        "post-install-cmd": [
            "@php -r \"copy('.env.example', '.env');\" || true"
        ],
        "post-update-cmd": [
            "@php artisan migrate --force || true"
        ]
    }
}
```

### Script Types

```bash
# PHP callable
"post-install-cmd": ["App\\Setup::run"]

# Shell command (prefix with @)
"scripts": {
    "compress": "@tar -czf backup.tar.gz vendor/"
}

# Composer command (prefix with @composer)
"post-install-cmd": "@composer dump-autoload -o"
```

---

## 1.5 Managing Dependencies

### require vs require-dev

```bash
# Production dependency
composer require guzzlehttp/guzzle

# Development dependency
composer require --dev phpunit/phpunit

# Specific version
composer require "phpunit/phpunit:^10.0"

# Remove
composer remove phpunit/phpunit

# Update single package
composer update phpunit/phpunit

# Update all
composer update
```

### Private Repositories

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-company/private-package"
        },
        {
            "type": "path",
            "url": "./packages/*"
        },
        {
            "type": "composer",
            "url": "https://satis.yourcompany.com"
        }
    ]
}
```

| Type | Use Case |
|------|----------|
| `vcs` | GitHub/GitLab/Bitbucket private repos |
| `path` | Local packages during development |
| `composer` | Satis/Torreter/Packagist private repos |
| `artifact` | Zipped package archives |

### Authentication for Private Packages

```bash
# Configure GitHub token for private repos
composer config --global github-oauth.github.com YOUR_TOKEN

# Configure GitLab token
composer config --global gitlab-oauth.gitlab.com YOUR_TOKEN

# Or use auth.json (don't commit this!)
# auth.json
{
    "github-oauth": {
        "github.com": "YOUR_TOKEN"
    }
}
```

---

## 1.6 Troubleshooting Common Issues

### Problem: "Allowed memory exhausted"

```bash
# Set memory limit for Composer
COMPOSER_MEMORY_LIMIT=-1 composer install

# Or in php.ini
memory_limit = -1
```

### Problem: Outdated lock file

```bash
# composer.lock was updated but composer.json wasn't
composer validate  # Checks for mismatch

# Sync lock with json
composer update --lock
```

### Problem: Conflicts between packages

```bash
# Debug why a version was selected
composer why vendor/package

# Show all versions available
composer show vendor/package --all

# Force a specific version
composer require "vendor/package:1.5.0" --no-update
composer update vendor/package
```

### Problem: Slow installs in CI

```bash
# Cache vendor directory
# GitHub Actions example
- uses: actions/cache@v3
  with:
    path: vendor/
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}

# Use --no-dev for production
composer install --no-dev --optimize-autoloader

# Use --prefer-dist (avoids git clones)
composer install --prefer-dist
```

### Problem: Platform package mismatch

```json
{
    "config": {
        "platform": {
            "php": "8.2.10",
            "ext-gd": "8.2"
        }
    }
}
```

This tells Composer to pretend the platform has specific versions, useful when deploying to an environment different from your local machine.

---

## 1.7 Plugins and Custom Commands

### Creating a Composer Plugin

```php
<?php
// src/MyPlugin.php
namespace Vendor\MyPlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;

class MyPlugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        $io->write('MyPlugin activated!');
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'post-install-cmd' => 'onPostInstall',
        ];
    }

    public function onPostInstall(\Composer\Script\Event $event): void
    {
        $event->getIO()->write('Running post-install tasks...');
    }
}
```

```json
{
    "type": "composer-plugin",
    "require": {
        "composer-plugin-api": "^2.0"
    },
    "extra": {
        "class": "Vendor\\MyPlugin\\MyPlugin"
    }
}
```

---

## 1.8 Exercises

1. Create a Composer package from scratch with PSR-4 autoloading and publish it to Packagist
2. Add PHPStan at max level and fix all discovered issues
3. Create a CI script pipeline (`@test`, `@analyse`, `@cs-check`) that runs together
4. Add a GitHub Actions workflow that runs your package tests on push
5. Version your package with `git tag v1.0.0` and set up Packagist auto-update
6. Configure `composer.lock` for an application and demonstrate the difference between `composer install` and `composer update`
7. Set up a private Satis repository and require a package from it
8. Create a Composer plugin that logs install times
9. Benchmark autoloading: compare default vs `-o` vs `-a` vs `--apcu` modes
10. Debug a version conflict using `composer why` and `composer why-not`

---

## Further Reading

- **Doc:** [Composer Documentation](https://getcomposer.org/doc/)
- **Doc:** [Semantic Versioning](https://semver.org/)
- **Doc:** [Packagist](https://packagist.org/)
- **Doc:** [Composer Scripts](https://getcomposer.org/doc/articles/scripts.md)
- **Doc:** [Private Packages with Satis](https://getcomposer.org/doc/articles/handling-private-packages.md)
- **Book:** "A Gentle Introduction to Composer" by Markus Tressl
