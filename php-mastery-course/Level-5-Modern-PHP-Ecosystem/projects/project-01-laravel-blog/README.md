# Project 1: Laravel Blog with Admin Panel

A complete Laravel 11 blog application with a full admin panel, built from scratch without Laravel Breeze or Jetstream. Demonstrates fundamental Laravel concepts alongside advanced features.

## Modern PHP Ecosystem Concepts Demonstrated

- **Composer** - Dependency management with `laravel/framework` and ecosystem packages
- **PSR-4** - Autoloading via `composer.json` psr-4 mapping
- **Laravel Eloquent ORM** - Relationships (BelongsTo, HasMany, BelongsToMany), scopes, accessors, mutators
- **Laravel Service Container & DI** - `PostService` injected into controllers
- **Laravel Queues** - `ShouldQueue` on `CommentNotification` for async email delivery
- **Laravel Events** - `Registered` event on new user creation
- **Laravel Notifications** - Mail + database notification channels
- **Laravel Middleware** - Custom `AdminMiddleware`, built-in auth/guest middleware
- **Laravel Form Requests** - `StorePostRequest`, `StoreCommentRequest` with validation
- **Blade Templating** - Layouts, sections, includes, component-like patterns
- **File Storage** - Local and S3 disk support for featured images
- **Pagination** - Built-in `paginate()` with query strings
- **RSS Feeds & Sitemaps** - XML generation for SEO
- **Database Migrations & Seeders** - Schema definition and test data
- **Model Factories** - Faker-powered factories for testing/seeding

## Architecture

```
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Auth, Post, Comment, Admin, RSS, Sitemap
│   │   ├── Requests/         # Form validation rules
│   │   └── Middleware/       # AdminMiddleware
│   ├── Models/               # User, Post, Category, Tag, Comment
│   ├── Notifications/        # CommentNotification (queued)
│   └── Services/             # PostService (upload, CRUD logic)
├── config/                   # App, database, auth, filesystems, queue
├── database/
│   ├── factories/            # Faker factories for all models
│   ├── migrations/           # Users, posts, categories, tags, comments
│   └── seeders/              # DatabaseSeeder with sample data
├── resources/views/
│   ├── layouts/              # Base app layout
│   ├── auth/                 # Login and Register
│   ├── posts/                # Public post listing & detail
│   ├── categories/           # Category filtered view
│   ├── tags/                 # Tag filtered view
│   └── admin/                # Dashboard, post CRUD, user management
└── routes/web.php            # All web routes
```

## Setup Instructions

```bash
# 1. Create the project
composer create-project --prefer-dist laravel/laravel laravel-blog
# or manually set up with this directory structure

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_DATABASE=laravel_blog
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Seed the database
php artisan db:seed

# 8. Create storage symlink
php artisan storage:link

# 9. Start the development server
php artisan serve
```

### Queue Setup (for email notifications)

```bash
# Create the jobs table
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

Admin credentials after seeding: `admin@example.com` / `password`

## Features

- **Authentication** - Register, login, logout (no Breeze/Jetstream)
- **Posts** - Rich text editing with TinyMCE, featured image upload
- **Categories & Tags** - Many-to-many tag relationships
- **Comments** - Approval workflow, notification to post author
- **Admin Panel** - Dashboard with stats, post management, user management
- **SEO** - SEO-friendly slugs, meta descriptions, sitemap.xml
- **RSS Feed** - Full RSS 2.0 feed
- **Search** - Full-text post search
- **Pagination** - 9 posts per page
- **File Uploads** - Featured images with storage abstraction

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=PostTest

# Run with coverage
php artisan test --coverage
```

## Code Quality Tools

- **Laravel Pint** - PHP CS Fixer wrapper (`./vendor/bin/pint`)
- **PHPStan** - Static analysis (add `larastan/larastan` for Laravel support)
- **PHPUnit** - Testing framework
