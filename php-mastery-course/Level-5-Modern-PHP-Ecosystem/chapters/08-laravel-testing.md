# Chapter 8: Laravel Testing

## Learning Objectives

By the end of this chapter you will:
- Write comprehensive HTTP tests with authentication and validation
- Use model factories with states and sequences
- Fake mail, notifications, queues, events, and storage
- Test scheduled tasks and Artisan commands
- Use Laravel Dusk for browser testing
- Write expressive tests with Pest for Laravel

---

## 8.1 HTTP Tests

```php
<?php
namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guests_cannot_create_posts(): void
    {
        $response = $this->postJson('/posts', [
            'title' => 'My Post',
            'content' => 'Content',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/posts', [
                'title' => 'Test Post Title',
                'content' => str_repeat('Content ', 30),
                'category_id' => Category::factory()->create()->id,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_post_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/posts', []);

        $response->assertInvalid(['title', 'content']);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_post_list_is_paginated(): void
    {
        Post::factory()->count(30)->create();

        $response = $this->get('/posts');

        $response->assertOk();
        $response->assertViewHas('posts');
        $response->assertSee('Next'); // Pagination link
    }

    public function test_post_shows_correct_data(): void
    {
        $post = Post::factory()
            ->for($this->user, 'author')
            ->has(Comment::factory()->count(5), 'comments')
            ->create();

        $response = $this->get("/posts/{$post->id}");

        $response->assertOk();
        $response->assertSee($post->title);
        $response->assertSee($post->content);
    }

    public function test_post_can_be_updated_by_owner(): void
    {
        $post = Post::factory()->for($this->user, 'author')->create();

        $response = $this->actingAs($this->user)
            ->patch("/posts/{$post->id}", [
                'title' => 'Updated Title',
                'content' => $post->content,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_post_cannot_be_updated_by_other_user(): void
    {
        $post = Post::factory()->create(); // Different user
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->patch("/posts/{$post->id}", [
                'title' => 'Hacked',
                'content' => 'Hacked content',
            ]);

        $response->assertForbidden();
    }

    public function test_post_can_be_deleted(): void
    {
        $post = Post::factory()->unpublished()->for($this->user, 'author')->create();

        $response = $this->actingAs($this->user)
            ->delete("/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertModelMissing($post);
        // Or for soft deletes:
        $this->assertSoftDeleted($post);
    }
}
```

---

## 8.2 API Tests

```php
<?php
use Laravel\Sanctum\Sanctum;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_posts(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'title', 'content', 'author']],
                'meta' => ['current_page', 'last_page', 'total'],
            ]);
    }

    public function test_authenticated_user_can_create_post_via_api(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/posts', [
            'title' => 'API Post',
            'content' => 'Content body here',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'API Post')
            ->assertJsonPath('data.author.id', auth()->id());
    }

    public function test_api_returns_404_for_missing_post(): void
    {
        $response = $this->getJson('/api/posts/99999');

        $response->assertNotFound()
            ->assertJson(['message' => 'Post not found']);
    }

    public function test_unauthenticated_api_request_fails(): void
    {
        $response = $this->postJson('/api/posts', []);

        $response->assertUnauthorized();
    }

    public function test_api_rate_limiting(): void
    {
        Sanctum::actingAs(User::factory()->create());

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/posts');
        }

        $response = $this->getJson('/api/posts');
        $response->assertStatus(429); // Too Many Requests
    }
}
```

---

## 8.3 Model Factory States and Sequences

```php
<?php
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'views' => 0,
            'published_at' => now(),
        ];
    }

    // States
    public function unpublished(): static
    {
        return $this->state(fn() => [
            'published_at' => null,
        ]);
    }

    public function trending(): static
    {
        return $this->state(fn() => [
            'views' => fake()->numberBetween(1000, 10000),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn() => [
            'published_at' => null,
            'title' => fake()->words(3, true),
        ]);
    }
}

// Using sequences for unique attributes
User::factory()
    ->count(10)
    ->sequence(fn($sequence) => [
        'email' => "user{$sequence->index}@example.com",
        'name' => "User {$sequence->index}",
    ])
    ->create();

// Alternating states
Post::factory()
    ->count(4)
    ->sequence(
        ['views' => 10],
        ['views' => 20],
        ['views' => 30],
        ['views' => 40],
    )
    ->create();

// Using states
$user = User::factory()
    ->has(Post::factory()->count(5)->unpublished()->trending())
    ->create();
```

---

## 8.4 Faking Services

```php
<?php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_is_sent_when_post_published(): void
    {
        Mail::fake();

        Post::factory()->create();
        // assert nothing sent
        Mail::assertNothingSent();

        // After publishing
        $this->post(route('posts.publish'), [...]);

        Mail::assertSent(PostPublishedMail::class, function ($mail) {
            return $mail->post->id === 1;
        });

        Mail::assertSentCount(1);
        Mail::assertQueued(PostPublishedMail::class, 3); // Queued 3 times
    }

    public function test_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $user->notify(new WelcomeNotification($user));

        Notification::assertSentTo(
            $user,
            WelcomeNotification::class,
            function ($notification, $channels) {
                return in_array('mail', $channels);
            }
        );

        Notification::assertCount(1);
    }

    public function test_jobs_are_dispatched(): void
    {
        Queue::fake();

        Post::factory()->create(); // No jobs

        $this->actingAs(User::factory()->create())
            ->post('/posts', [...]);

        Queue::assertPushed(ProcessPost::class, function ($job) {
            return $job->post->title === 'Test';
        });

        Queue::assertNotPushed(SendWelcomeEmail::class);
        Queue::assertPushedOn('high', UrgentJob::class);
    }

    public function test_events_are_dispatched(): void
    {
        Event::fake([PostCreated::class]);

        Post::factory()->create();

        Event::assertDispatched(PostCreated::class);
        Event::assertDispatchedTimes(PostCreated::class, 1);

        // Only PostCreated is faked; real events still fire
        Event::assertNotDispatched(PostDeleted::class);
    }

    public function test_storage_operations(): void
    {
        Storage::fake('s3');

        $response = $this->actingAs(User::factory()->create())
            ->post('/photos', [
                'photo' => UploadedFile::fake()->image('photo.jpg', 500, 500),
            ]);

        Storage::disk('s3')->assertExists('photos/1.jpg');
        Storage::disk('s3')->assertMissing('photos/old.jpg');

        // Assert file size
        Storage::disk('s3')->assertExists('photos/1.jpg', 60 * 1024); // ~60KB
    }
}
```

---

## 8.5 Console Tests

```php
<?php
class ImportUsersCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_imports_csv(): void
    {
        Storage::fake('local');
        Storage::put('users.csv', "name,email\nJohn,john@test.com\nJane,jane@test.com");

        $this->artisan('users:import', [
            'file' => Storage::path('users.csv'),
        ])->expectsOutput('Importing from: ...')
          ->expectsQuestion('Do you want to send welcome emails?', 'no')
          ->assertSuccessful();

        $this->assertDatabaseCount('users', 2);
    }

    public function test_command_handles_missing_file(): void
    {
        $this->artisan('users:import', [
            'file' => '/nonexistent.csv',
        ])->expectsOutput('File not found: /nonexistent.csv')
          ->assertExitCode(Command::FAILURE);
    }

    public function test_dry_run_does_not_persist(): void
    {
        Storage::fake('local');
        Storage::put('users.csv', "name,email\nJohn,john@test.com");

        $this->artisan('users:import', [
            'file' => Storage::path('users.csv'),
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseCount('users', 0);
    }
}
```

### Scheduled Task Tests

```php
<?php
class ScheduleTest extends TestCase
{
    public function test_commands_are_scheduled(): void
    {
        $this->assertCommandInSchedule('newsletter:send');

        // Assert frequency
        $schedule = app(Kernel::class)->getSchedule();
        $events = $schedule->events();

        $this->assertNotEmpty($events);
    }

    private function assertCommandInSchedule(string $command): void
    {
        $schedule = app(Kernel::class)->getSchedule();
        $events = $schedule->events();

        foreach ($events as $event) {
            if (str_contains($event->command ?? '', $command)) {
                $this->assertTrue(true);
                return;
            }
        }

        $this->fail("Command {$command} not found in schedule");
    }
}
```

---

## 8.6 Browser Tests (Laravel Dusk)

```php
<?php
namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PostBrowserTest extends DuskTestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/posts/create')
                ->type('title', 'Browser Test Post')
                ->type('content', str_repeat('Content ', 30))
                ->press('Publish')
                ->assertPathIs('/posts')
                ->assertSee('Post created successfully')
                ->assertSee('Browser Test Post');
        });
    }

    public function test_validation_errors_display(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/posts/create')
                ->press('Publish')
                ->assertSee('The title field is required')
                ->assertSee('The content field is required');
        });
    }

    public function test_search_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/posts')
                ->type('search', 'Laravel')
                ->waitForText('Laravel Tutorial')
                ->assertSee('Laravel Tutorial')
                ->assertDontSee('Vue.js Guide');
        });
    }

    public function test_responsive_design(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(375, 812) // iPhone X
                ->assertVisible('.mobile-menu')
                ->resize(1024, 768) // iPad
                ->assertVisible('.desktop-sidebar');
        });
    }

    public function test_ajax_comment_submission(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($post, $user) {
            $browser->loginAs($user)
                ->visit("/posts/{$post->id}")
                ->type('comment', 'Great post!')
                ->press('Submit Comment')
                ->waitForText('Great post!')
                ->assertSee('Great post!');
        });
    }

    public function test_file_upload(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/settings/profile')
                ->attach('avatar', __DIR__.'/stubs/avatar.jpg')
                ->press('Save')
                ->waitForText('Profile updated')
                ->assertSee('Profile updated');
        });
    }
}
```

### Dusk Configuration

```php
<?php
// tests/DuskTestCase.php
abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function driver(): Browser
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless=new',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
```

---

## 8.7 Pest for Laravel

```php
<?php
// tests/Feature/PostTest.php
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('allows authenticated user to create a post', function () {
    actingAs($this->user);

    post('/posts', [
        'title' => 'Pest Test',
        'content' => 'Testing with Pest PHP',
        'category_id' => Category::factory()->create()->id,
    ])->assertRedirect();

    assertDatabaseHas('posts', [
        'title' => 'Pest Test',
        'user_id' => $this->user->id,
    ]);
});

it('rejects unauthenticated requests', function () {
    post('/posts', [
        'title' => 'Hack',
        'content' => 'Attempt',
    ])->assertUnauthorized();
});

it('validates required fields', function () {
    actingAs(User::factory()->create());

    post('/posts', [])
        ->assertInvalid(['title', 'content']);
});

it('only shows published posts', function () {
    Post::factory()->published()->count(3)->create();
    Post::factory()->unpublished()->count(2)->create();

    $response = get('/posts');
    $response->assertSee('posts', 3); // Only 3 visible
});

it('belongs to an author', function () {
    $post = Post::factory()
        ->for(User::factory(), 'author')
        ->create();

    expect($post->author)->toBeInstanceOf(User::class);
});

dataset('post_titles', [
    'Short title' => 'Hi',
    'Valid title' => 'A valid post title',
    'Long title' => str_repeat('A', 256),
]);

it('validates title length', function (string $title) {
    actingAs(User::factory()->create());

    $response = post('/posts', [
        'title' => $title,
        'content' => 'Valid content here',
    ]);

    if (strlen($title) < 3 || strlen($title) > 255) {
        $response->assertInvalid(['title']);
    } else {
        $response->assertValid();
    }
})->with('post_titles');
```

---

## 8.8 Database Testing

```php
<?php
class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_count_queries(): void
    {
        Post::factory()->count(20)->create();

        // Assert query count
        DB::enableQueryLog();
        $posts = Post::all();
        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_n_plus_one_detection(): void
    {
        Post::factory()->count(10)->hasComments(3)->create();

        // Without eager loading — N+1 (11 queries)
        DB::enableQueryLog();
        $posts = Post::all();
        foreach ($posts as $post) {
            $this->assertNotEmpty($post->comments);
        }
        $this->assertLessThanOrEqual(11, count(DB::getQueryLog()));
        DB::disableQueryLog();
    }

    public function test_upsert(): void
    {
        User::factory()->create(['email' => 'test@test.com']);

        User::upsert([
            ['email' => 'test@test.com', 'name' => 'Updated'],
            ['email' => 'new@test.com', 'name' => 'New User'],
        ], ['email'], ['name']);

        $this->assertDatabaseHas('users', ['name' => 'Updated']);
        $this->assertDatabaseCount('users', 2);
    }
}
```

---

## 8.9 Exercises

1. Write HTTP tests for authentication, CRUD, and validation edges
2. Create factory states and sequences for a complex model
3. Fake mail, queue, and storage in a feature test
4. Test an Artisan command with inputs and file operations
5. Write a Pest test suite for a Laravel API

---

## 8.10 Interview Questions

1. "What's the difference between `RefreshDatabase` and `DatabaseTransactions`?"
2. "How do you test queue jobs without actually running them?"
3. "What is `withExceptionHandling()` and when would you use it?"
4. "How does Pest differ from PHPUnit for Laravel testing?"
5. "How would you test that an email was sent with specific content?"

---

## Further Reading

- **Doc:** [Laravel HTTP Tests](https://laravel.com/docs/http-tests)
- **Doc:** [Laravel Dusk](https://laravel.com/docs/dusk)
- **Doc:** [Laravel Database Testing](https://laravel.com/docs/database-testing)
- **Doc:** [Laravel Mocking](https://laravel.com/docs/mocking)
- **Doc:** [Pest for Laravel](https://pestphp.com/docs/guides/laravel-testing)
- **Book:** "Laravel Testing Decoded" by Jason McCreary

---

*End of Chapter 8. Proceed to Chapter 9: Laravel Packages.*
