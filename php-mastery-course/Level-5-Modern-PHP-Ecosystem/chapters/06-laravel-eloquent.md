# Chapter 6: Laravel Eloquent

## Learning Objectives

By the end of this chapter you will:
- Master Eloquent relationships, scopes, accessors, and mutators
- Optimize queries with eager loading, subquery selects, and caching
- Use advanced features: model events, factories, and casts
- Diagnose and fix N+1 query problems
- Implement model observers and soft deletes

---

## 6.1 Eloquent Models: Conventions

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    // Table name (optional: defaults to snake_case plural of class)
    protected $table = 'blog_posts';

    // Primary key type (default: 'id')
    protected $primaryKey = 'uuid';
    public $incrementing = false;       // For UUIDs
    protected $keyType = 'string';

    // Timestamps (default: true)
    public $timestamps = true;

    // Allowed for mass assignment
    protected $fillable = [
        'title', 'slug', 'content', 'category_id',
        'published_at', 'is_published',
    ];

    // Guarded (opposite of fillable)
    protected $guarded = ['id', 'is_admin'];

    // Hidden from JSON/array serialization
    protected $hidden = ['password', 'remember_token'];

    // Appended (computed attributes in JSON)
    protected $appends = ['excerpt'];

    // Default attribute values
    protected $attributes = [
        'is_published' => false,
        'views' => 0,
    ];

    // Casts
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'meta' => 'array',
        'settings' => AsEnumCollection::class.':'.PostSetting::class,
    ];
}
```

---

## 6.2 Relationships

```php
<?php
class Post extends Model
{
    // One-to-One
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // One-to-Many (inverse)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withDefault(['name' => 'Deleted User']);
    }

    // One-to-Many
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->latest(); // default ordering
    }

    // Many-to-Many
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps()          // tracks pivot timestamps
            ->withPivot('order')        // extra pivot columns
            ->using(Taggable::class);   // custom pivot model
    }

    // Has-Many-Through (e.g., posts through users to countries)
    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, User::class);
    }

    // Has-One-Through
    public function subscription(): HasOneThrough
    {
        return $this->hasOneThrough(Subscription::class, User::class);
    }

    // Polymorphic (e.g., comments on both posts and videos)
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Many-to-Many Polymorphic (e.g., tags on posts, videos, products)
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // Belongs-To-Many Polymorphic (inverse)
    public function posts(): MorphToMany
    {
        return $this->morphToMany(Post::class, 'taggable');
    }
}
```

---

## 6.3 Query Scopes

```php
<?php
class Post extends Model
{
    // Local scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeTrending(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('published_at', [now()->subDays($days), now()])
            ->orderByDesc('views');
    }

    public function scopeByCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    // Dynamic scope with parameters
    public function scopeWhereHasComments(Builder $query, int $min): Builder
    {
        return $query->has('comments', '>=', $min);
    }
}

// Usage
$posts = Post::published()
    ->trending()
    ->byCategory('laravel')
    ->with('author:id,name')
    ->paginate(20);

// Global scopes
class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('is_active', true);
    }
}

class User extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new ActiveScope);
    }

    // Remove global scope when needed
    public static function withInactive(): Builder
    {
        return static::withoutGlobalScope(ActiveScope::class);
    }
}
```

---

## 6.4 Accessors, Mutators, and Casts

```php
<?php
class Post extends Model
{
    // Accessor (attribute-style access)
    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    // Accessor with caching (computed once per model)
    public function getReadingTimeAttribute(): int
    {
        return (int) ceil(str_word_count($this->content) / 200);
    }

    // Mutator (set before save)
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = str($value)->slug();
    }

    // Mutator with side effect
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }
}

// Custom cast
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        return new Money($value, new Currency('USD'));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value instanceof Money ? $value->amount() : $value;
    }
}

class Product extends Model
{
    protected $casts = [
        'price' => MoneyCast::class,
    ];
}
```

---

## 6.5 Eager Loading

```php
<?php
// N+1 problem — BAD
$posts = Post::all(); // 1 query
foreach ($posts as $post) {
    echo $post->author->name; // N queries!
}
// Total: 1 + N = 1 + 100 = 101 queries

// Eager loading — GOOD
$posts = Post::with('author')->get(); // 1 + 1 = 2 queries

// Nested eager loading
$posts = Post::with(['author', 'comments.user', 'tags'])->get();

// Constrained eager loading
$posts = Post::with(['comments' => function ($query) {
    $query->where('is_approved', true)->latest()->limit(5);
}])->get();

// Lazy eager loading (when you can't load at query time)
$posts->load(['comments' => fn($q) => $q->where('is_approved', true)]);

// Load missing (won't reload if already loaded)
$posts->loadMissing('comments');

// Eager load count
$posts = Post::withCount('comments', 'likes as upvotes', 'tags')->get();
// $post->comments_count, $post->upvotes, $post->tags_count

// Eager load exists
$users = User::withExists('posts')->get();
// $user->posts_exists (bool)
```

### Subquery Selects

```php
<?php
// Add last comment from subquery
$posts = Post::addSelect([
    'last_comment_id' => Comment::select('id')
        ->whereColumn('post_id', 'posts.id')
        ->latest()
        ->limit(1),
])->with('lastComment'); // Then eager load the relationship

// Order by subquery
$posts = Post::orderByDesc(
    Comment::select('created_at')
        ->whereColumn('post_id', 'posts.id')
        ->latest()
        ->limit(1)
)->get();
```

---

## 6.6 Model Events and Observers

```php
<?php
class Post extends Model
{
    protected static function booted(): void
    {
        // Fires on creation
        static::creating(function (Post $post) {
            if (! $post->uuid) {
                $post->uuid = (string) Str::uuid();
            }
        });

        // Fires after creation
        static::created(function (Post $post) {
            Log::info('Post created', ['id' => $post->id]);
        });

        // Fires on update
        static::updating(function (Post $post) {
            $post->timestamps = false; // Skip updated_at
        });

        static::updated(function (Post $post) {
            Cache::forget('post_'.$post->id);
        });

        // Fires on deletion (before)
        static::deleting(function (Post $post) {
            $post->comments()->delete(); // Cascade
        });

        static::deleted(function (Post $post) {
            event(new PostDeleted($post));
        });

        // Retrieving (after fetch from DB)
        static::retrieved(function (Post $post) {
            //
        });

        // Saving / Saved (fires for both create and update)
        static::saving(fn($p) => $p->searchable());
        static::saved(fn($p) => Cache::forget('recent_posts'));

        // Restoring / Restored (for soft deletes)
        static::restored(fn($p) => $p->searchable());
    }
}

// Observer class (cleaner for many events)
php artisan make:observer PostObserver --model=Post

class PostObserver
{
    public function creating(Post $post): void { /* ... */ }
    public function created(Post $post): void { /* ... */ }
    public function updating(Post $post): void { /* ... */ }
    public function updated(Post $post): void { /* ... */ }
    public function saving(Post $post): void { /* ... */ }
    public function saved(Post $post): void { /* ... */ }
    public function deleting(Post $post): void { /* ... */ }
    public function deleted(Post $post): void { /* ... */ }
    public function restoring(Post $post): void { /* ... */ }
    public function restored(Post $post): void { /* ... */ }
    public function retrieved(Post $post): void { /* ... */ }
}

// Register in AppServiceProvider or EventServiceProvider
public function boot(): void
{
    Post::observe(PostObserver::class);
}
```

---

## 6.7 Model Factories and Seeders

```php
<?php
namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(),
            'slug' => fn(array $attr) => str($attr['title'])->slug(),
            'content' => fake()->paragraphs(5, true),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'published_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'is_published' => true,
        ];
    }

    // States — override specific attributes
    public function unpublished(): static
    {
        return $this->state(fn() => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn() => [
            'views' => fake()->numberBetween(1000, 10000),
        ]);
    }

    // After creating
    public function configure(): static
    {
        return $this->afterCreating(function (Post $post) {
            $post->tags()->attach(Tag::inRandomOrder()->limit(3)->pluck('id'));
        });
    }
}
```

```php
<?php
// DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();
        Category::factory(5)->create();
        Post::factory(50)
            ->unpublished()
            ->has(Comment::factory()->count(3), 'comments')
            ->create();
        Post::factory(200)
            ->featured()
            ->has(Comment::factory()->count(10), 'comments')
            ->create();
    }
}
```

---

## 6.8 Performance Optimization

```php
<?php
// Chunking (memory-efficient for large datasets)
Post::where('is_published', true)->chunk(100, function ($posts) {
    foreach ($posts as $post) {
        ProcessPost::dispatch($post);
    }
});

// Lazy loading (cursor for very large datasets)
foreach (Post::lazy(500) as $post) {
    // Memory: only 500 models at a time
}

// Chunk by ID (stable for tables that change mid-iteration)
Post::where('is_published', true)->chunkById(100, function ($posts) {
    foreach ($posts as $post) {
        $post->update(['indexed' => true]);
    }
});

// Cursor (uses generators, lowest memory)
foreach (Post::cursor() as $post) {
    // One query, yields models one at a time
}

// Subquery ordering (avoid extra joins)
$users = User::orderByDesc(
    Post::select('created_at')
        ->whereColumn('user_id', 'users.id')
        ->latest()
        ->limit(1)
)->get();

// WhereHas vs Join
// Use whereHas for existence (clearer intent)
// Use join for filtering by related columns
$posts = Post::whereHas('tags', fn($q) => $q->where('name', 'PHP'));
$posts = Post::join('post_tag', 'posts.id', '=', 'post_tag.post_id')
    ->join('tags', 'post_tag.tag_id', '=', 'tags.id')
    ->where('tags.name', 'PHP')
    ->select('posts.*')
    ->distinct()
    ->get();

// N+1 detective
use Barryvdh\Debugbar\Facades\Debugbar;
Debugbar::enable();
// Also: Clockwork, Laravel Telescope, or written middleware:
class DetectNPlusOne
{
    public function handle(Request $request, Closure $next): mixed
    {
        DB::enableQueryLog();
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (count(DB::getQueryLog()) > 20) {
            Log::warning('High query count', [
                'url' => $request->fullUrl(),
                'queries' => count(DB::getQueryLog()),
            ]);
        }
    }
}
```

---

## 6.9 Soft Deletes

```php
<?php
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
}

// Soft delete
$post->delete(); // Sets deleted_at, doesn't remove row

// Include soft-deleted
Post::withTrashed()->get();

// Only soft-deleted
Post::onlyTrashed()->get();

// Restore
$post->restore();

// Force delete (real delete)
$post->forceDelete();

// Check if soft-deleted
if ($post->trashed()) { /* ... */ }

// With relationships
$user->posts()->withTrashed()->get();
```

---

## 6.10 Exercises

1. Define a `Post` with relationships, scopes, accessors, and casts
2. Write queries demonstrating all 4 eager loading patterns
3. Create a factory with 3 states and a seeder with relationships
4. Build a model observer for cache invalidation
5. Diagnose and fix an N+1 query in a controller

---

## 6.11 Interview Questions

1. "What is the N+1 problem and how do you solve it?"
2. "Explain local vs global scopes and when to use each."
3. "What's the difference between `withCount` and `loadCount`?"
4. "How do model events differ from observers?"
5. "When would you use `chunk()` vs `cursor()` vs `lazy()`?"

---

## Further Reading

- **Doc:** [Laravel Eloquent](https://laravel.com/docs/eloquent)
- **Doc:** [Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
- **Doc:** [Eloquent Factories](https://laravel.com/docs/eloquent-factories)
- **Doc:** [Eloquent Serialization](https://laravel.com/docs/eloquent-serialization)
- **Article:** [Eloquent Performance Patterns](https://laravel-news.com/eloquent-performance-patterns)

---

*End of Chapter 6. Proceed to Chapter 7: Laravel Advanced.*
