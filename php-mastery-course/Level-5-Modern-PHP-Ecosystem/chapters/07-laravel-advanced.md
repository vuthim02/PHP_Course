# Chapter 7: Laravel Advanced

## Learning Objectives

By the end of this chapter you will:
- Build and dispatch queued jobs (sync, async, batched, chain)
- Send mail with Markdown templates and notifications via channels
- Implement events, listeners, and subscribers
- Schedule tasks, write custom Artisan commands, and build service providers
- Use authorization with policies and gates
- Leverage filesystem and cloud storage

---

## 7.1 Queues

```env
# .env — choose your queue driver
QUEUE_CONNECTION=database    # MySQL/PostgreSQL/SQLite
# QUEUE_CONNECTION=redis      # Redis (for production)
# QUEUE_CONNECTION=sqs        # AWS SQS
# QUEUE_CONNECTION=beanstalkd # Beanstalkd
```

```php
<?php
// Generate migration for database driver
// php artisan queue:table && php artisan migrate

// Generate job class
// php artisan make:job ProcessPost
// php artisan make:job SendWelcomeEmail --queued

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Max attempts
    public $tries = 3;

    // Timeout per attempt (seconds)
    public $timeout = 30;

    // Backoff (retry delay)
    public $backoff = [2, 10, 30]; // progressive

    // Rate limiting
    public $maxExceptions = 3;

    // Queue name
    public function __construct(
        public User $user,
        public string $queue = 'emails',
    ) {}

    public function handle(MailService $mail): void
    {
        $mail->sendWelcome($this->user);

        // Job failed? $this->fail($exception);
        // Release back to queue: $this->release(10);
        // Delete (mark as failed): $this->delete();
    }

    // Handle job failure
    public function failed(Throwable $e): void
    {
        Log::error('Welcome email failed', [
            'user_id' => $this->user->id,
            'error' => $e->getMessage(),
        ]);
    }

    // Delete if model no longer exists
    public function deleteWhenMissingModels(): bool
    {
        return true;
    }
}
```

### Dispatching Jobs

```php
<?php
// Simple dispatch
SendWelcomeEmail::dispatch($user);

// Delayed dispatch
SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(5));

// On specific queue/connection
SendWelcomeEmail::dispatch($user)
    ->onQueue('emails')
    ->onConnection('redis');

// After commit (only dispatch if DB transaction succeeds)
SendWelcomeEmail::dispatchAfterResponse($user);

// Dispatch chain (sequential)
Bus::chain([
    new ProcessOrder($order),
    new ChargeCustomer($order),
    new SendOrderConfirmation($order),
])->onConnection('redis')->dispatch();

// Batch jobs
$batch = Bus::batch([
    new ProcessPost($post1),
    new ProcessPost($post2),
    new ProcessPost($post3),
])->then(function () {
    // All jobs complete
})->catch(function (Batch $batch, Throwable $e) {
    // A job failed
})->finally(function (Batch $batch) {
    // Batch finished (success or fail)
})->dispatch();

// Get batch status
$batch = Bus::findBatch($batchId);
$batch->progressPercentage(); // int
$batch->finished(); // bool
$batch->hasFailures(); // bool
```

### Running Queues

```bash
# Basic worker (process jobs forever)
php artisan queue:work

# Specify queue and connection
php artisan queue:work redis --queue=emails,default --tries=3

# Process single job (useful for testing)
php artisan queue:work --once

# Daemon options
php artisan queue:work --sleep=3 --timeout=60 --max-jobs=1000

# Supervisor config (/etc/supervisor/conf.d/laravel-worker.conf):
# [program:laravel-worker]
# process_name=%(program_name)s_%(process_num)02d
# command=php /var/www/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
# autostart=true
# autorestart=true
# stopasgroup=true
# killasgroup=true
# user=forge
# numprocs=8
# redirect_stderr=true
# stdout_logfile=/var/log/laravel-worker.log
# stopwaitsecs=3600
```

### Job Middleware

```php
<?php
// Rate limit job
use Illuminate\Queue\Middleware\RateLimited;

public function middleware(): array
{
    return [
        new RateLimited('stripe'),
    ];
}

// Prevent duplicate jobs
use Illuminate\Queue\Middleware\WithoutOverlapping;

public function middleware(): array
{
    return [(new WithoutOverlapping($this->user->id))->releaseAfter(30)];
}
```

---

## 7.2 Mail

```php
<?php
// Generate mail class
// php artisan make:mail OrderConfirmation --markdown=emails.orders.confirmed

class OrderConfirmation extends Mailable
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation #'.$this->order->id,
            from: new Address('orders@example.com', 'Store'),
            replyTo: [new Address('support@example.com', 'Support')],
            tags: ['order-confirmation'],
            metadata: ['order_id' => $this->order->id],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmed',
            with: [
                'order' => $this->order,
                'total' => $this->order->total,
                'items' => $this->order->items()->with('product')->get(),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('s3', "invoices/{$this->order->id}.pdf")
                ->as('invoice.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
```

```blade
{{-- resources/views/emails/orders/confirmed.blade.php --}}
<x-mail::message>
# Order Confirmed!

Dear **{{ $order->user->name }}**,

Your order **#{{ $order->id }}** has been confirmed.

<x-mail::panel>
**Total:** {{ number_format($order->total, 2) }} USD
</x-mail::panel>

<x-mail::table>
| Product | Qty | Price |
|:--------|:---:|------:|
@foreach($items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('orders.show', $order)" color="success">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

```php
<?php
// Send mail
Mail::to($order->user)->send(new OrderConfirmation($order));
Mail::to($user)->queue(new OrderConfirmation($order)); // Queued
Mail::to($user)->later(now()->addMinutes(10), new OrderConfirmation($order));
Mail::to($request->user())->cc($admin)->bcc($manager)->send($mailable);

// Mail to multiple
Mail::to([$user1, $user2, $user3])->send(new Newsletter(...));
```

---

## 7.3 Notifications

```php
<?php
// Generate notification
// php artisan make:notification PaymentReceived

class PaymentReceived extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
    ) {}

    // Determine channels based on user preferences
    public function via(User $notifiable): array
    {
        return $notifiable->preferred_channels; // ['mail', 'database', 'slack']
    }

    // Mail channel
    public function toMail(User $notifiable): Mailable
    {
        return (new MailMessage)
            ->subject('Payment Received')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('We received your payment of $'.number_format($this->payment->amount, 2))
            ->action('View Details', route('payments.show', $this->payment))
            ->line('Thank you for your business!')
            ->line('payment_id: '.$this->payment->id)
            ->attachData($this->payment->receiptPdf(), 'receipt.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    // Database channel
    public function toDatabase(User $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'message' => "Payment of \${$this->payment->amount} received",
        ];
    }

    // Broadcast channel
    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'user' => $notifiable->only('id', 'name'),
        ]);
    }

    // Slack channel
    public function toSlack(User $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->success()
            ->content("Payment received from {$notifiable->name}")
            ->attachment(function ($attachment) {
                $attachment->title('Payment #'.$this->payment->id)
                    ->fields([
                        'Amount' => '$'.number_format($this->payment->amount, 2),
                        'Date' => $this->payment->created_at->toDateTimeString(),
                    ]);
            });
    }

    // Vonage (SMS) channel
    public function toVonage(User $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->content("Your payment of \${$this->payment->amount} was received.");
    }
}
```

```php
<?php
// Send notification
$user->notify(new PaymentReceived($payment));
Notification::send($users, new PaymentReceived($payment));
Notification::route('mail', 'user@example.com')
    ->route('slack', $slackWebhook)
    ->notify(new PaymentReceived($payment));

// Mark as read
$user->unreadNotifications->markAsRead();
$user->notifications()->where('id', $id)->delete();
```

---

## 7.4 Events, Listeners, and Subscribers

```php
<?php
// Define event
// php artisan make:event OrderShipped

class OrderShipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}
}

// Define listener
// php artisan make:listener SendShipmentNotification --event=OrderShipped

class SendShipmentNotification
{
    public function handle(OrderShipped $event): void
    {
        Mail::to($event->order->user)->send(
            new ShipmentConfirmation($event->order)
        );
    }

    public function failed(OrderShipped $event, Throwable $e): void
    {
        Log::error('Shipment notification failed', [
            'order' => $event->order->id,
            'error' => $e->getMessage(),
        ]);
    }
}

// EventServiceProvider mapping
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderShipped::class => [
            SendShipmentNotification::class,
            UpdateInventory::class,
            ChargeCustomer::class,
        ],
        PostCreated::class => [
            NotifySubscribers::class,
            UpdateSearchIndex::class,
            InvalidateCache::class,
        ],
    ];

    // Subscriber class (organize related listeners)
    protected $subscribe = [
        UserEventSubscriber::class,
    ];
}

// Subscriber
class UserEventSubscriber
{
    public function handleUserLogin(UserLoggedIn $event): void { /* ... */ }
    public function handleUserLogout(UserLoggedOut $event): void { /* ... */ }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            UserLoggedIn::class,
            [self::class, 'handleUserLogin']
        );
        $events->listen(
            UserLoggedOut::class,
            [self::class, 'handleUserLogout']
        );
    }
}

// Dispatch event
event(new OrderShipped($order));
OrderShipped::dispatch($order);
```

---

## 7.5 Artisan Commands

```php
<?php
// Generate command
// php artisan make:command ImportUsers --command=users:import

class ImportUsers extends Command
{
    protected $signature = 'users:import
        {file : Path to CSV file}
        {--dry-run : Preview without saving}
        {--batch=100 : Records per batch}
        {--source=local : Source identifier}';

    protected $description = 'Import users from CSV file';

    public function handle(): int
    {
        $file = $this->argument('file');
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $this->info("Importing from: {$file}");
        $this->warn('This is a warning message');
        $this->newLine();

        $bar = $this->output->createProgressBar(100);
        $bar->start();

        $records = array_chunk($this->parseCsv($file), $batchSize);
        $total = 0;

        foreach ($records as $batch) {
            if (! $dryRun) {
                User::upsert($batch, ['email'], ['name', 'role']);
            }

            $total += count($batch);
            $bar->advance(count($batch) / array_sum(array_map('count', $records)) * 100);
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("[DRY RUN] Would import {$total} users");
        } else {
            $this->info("Imported {$total} users");
        }

        // Ask for confirmation
        if ($this->confirm('Do you want to send welcome emails?')) {
            $this->call('emails:send-welcome', [
                'source' => $this->option('source'),
            ]);
        }

        return self::SUCCESS;
    }

    protected function parseCsv(string $file): array
    {
        // Parse CSV logic
    }
}

// Register in Kernel
protected $commands = [
    Commands\ImportUsers::class,
];
```

---

## 7.6 Task Scheduling

```php
<?php
// app/Console/Kernel.php
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Send daily digest at 8 AM
        $schedule->call(function () {
            User::where('digest_enabled', true)->chunk(100, function ($users) {
                SendDigestJob::dispatch($users);
            });
        })->dailyAt('08:00');

        // Artisan command every hour
        $schedule->command('import:csv --batch=200')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Queued job every 15 minutes
        $schedule->job(new GenerateReport, 'reports')
            ->everyFifteenMinutes()
            ->environments('production');

        // Send weekly newsletter (Monday 10 AM)
        $schedule->command('newsletter:send')
            ->weeklyOn(1, '10:00')
            ->timezone('America/New_York')
            ->emailOutputTo('admin@example.com');

        // Maintenance windows
        $schedule->command('maintenance:cleanup')
            ->daily()
            ->between('02:00', '04:00')
            ->when(function () {
                return ! Cache::get('maintenance_mode');
            });

        // Ping health check
        $schedule->call(fn() => Http::head('https://health.example.com/ping'))
            ->everyMinute()
            ->pingBefore(fn() => Log::info('Health check starting'))
            ->thenPing(fn() => Log::info('Health check passed'))
            ->pingOnFailure(fn() => Log::error('Health check failed'));
    }

    protected function shortSchedule(Schedule $schedule): void
    {
        // High-frequency tasks (Laravel Octane)
        $schedule->call(fn() => Cache::increment('heartbeat'))
            ->everySecond();
    }
}

// Run scheduler (add to crontab: * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1)
```

---

## 7.7 Broadcasting / WebSockets

```env
# .env
BROADCAST_DRIVER=pusher   # pusher, ably, redis, log, null
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
```

```php
<?php
// Event should implement ShouldBroadcast
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $status,
    ) {}

    // Broadcast on private channel
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders.'.$this->order->user_id);
    }

    // Custom event name
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    // Data sent to client
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->status,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    // Don't broadcast to current user
    public function broadcastWhen(): bool
    {
        return ! auth()->guest();
    }
}

// Broadcast event
event(new OrderStatusUpdated($order, 'shipped'));
```

```javascript
// resources/js/app.js
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// Listen
Echo.private(`orders.${userId}`)
    .listen(".order.status.updated", (e) => {
        console.log(`Order ${e.order_id}: ${e.status}`);
        updateOrderStatus(e.order_id, e.status);
    });

Echo.channel("public.news")
    .listen(".news.published", (e) => {
        showNotification(e.title);
    });

Echo.join(`room.${roomId}`)
    .here((users) => { /* online users */ })
    .joining((user) => { /* user joined */ })
    .leaving((user) => { /* user left */ })
    .listen(".message.sent", (e) => { /* new message */ });
```

### Channel Authorization

```php
<?php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('orders.{userId}', function (User $user, int $userId) {
    return $user->id === $userId; // Only the order owner
});

Broadcast::channel('admin.notifications', function (User $user) {
    return $user->isAdmin(); // Only admins
});
```

---

## 7.8 Authorization: Gates & Policies

```php
<?php
// app/Providers/AuthServiceProvider.php
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot(): void
    {
        // Gates (simple closures)
        Gate::define('view-dashboard', function (User $user) {
            return $user->role === 'admin' || $user->role === 'manager';
        });

        Gate::define('export-data', function (User $user) {
            return $user->hasPermission('export');
        });

        // Resource gates
        Gate::resource('posts', PostPolicy::class);
        // Creates: viewAny, view, create, update, delete, restore, forceDelete
    }
}

// Policy class
// php artisan make:policy PostPolicy --model=Post
class PostPolicy
{
    // Before check (runs before all policy methods)
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true; // Bypass all checks
        }
        return null;
    }

    // After check
    public function after(User $user, string $ability): ?bool
    {
        if ($user->isBanned()) {
            return false;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // Everyone can list posts
    }

    public function view(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $post->is_published;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id && ! $post->is_published;
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isEditor();
    }
}

// Usage in controller
$this->authorize('update', $post);
$this->authorize('create', Post::class);
$this->authorize('publish', $post);

// Using Gate facade
if (Gate::allows('view-dashboard')) { /* ... */ }
if (Gate::denies('export-data')) { abort(403); }
if (Gate::forUser($otherUser)->allows('update', $post)) { /* ... */ }

// In Blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Edit</a>
@endcan

@cannot('create', App\Models\Post::class)
    <p>You cannot create posts.</p>
@endcannot

@canany(['update', 'delete'], $post)
    <div class="actions">...</div>
@endcanany
```

---

## 7.9 File Storage

```php
<?php
use Illuminate\Support\Facades\Storage;

// Upload
$path = $request->file('avatar')->store('avatars', 's3');
$path = $request->file('avatar')->storeAs('avatars', $user->id.'.jpg', 'public');

// Store from raw contents
Storage::disk('s3')->put("posts/{$post->id}.html", $htmlContent);
Storage::disk('s3')->putFileAs('photos', new File($tmpPath), 'photo.jpg');

// Read
$contents = Storage::get('avatars/'.$user->avatar);
$exists = Storage::exists('avatars/'.$user->avatar);
$url = Storage::url('avatars/'.$user->avatar);
$temporaryUrl = Storage::temporaryUrl('invoices/'.$invoice->id.'.pdf', now()->addHours(24));

// Directory operations
Storage::makeDirectory('exports/2024');
$files = Storage::files('exports/2024'); // List files
$directories = Storage::directories('exports'); // List directories
Storage::deleteDirectory('exports/2024');

// Metadata
$size = Storage::size('avatars/'.$user->avatar);
$mime = Storage::mimeType('avatars/'.$user->avatar);
$lastModified = Storage::lastModified('avatars/'.$user->avatar);

// Visibility
Storage::setVisibility('avatars/'.$user->avatar, 'public');
$visibility = Storage::getVisibility('avatars/'.$user->avatar);

// Cloud copy/move
Storage::disk('s3')->copy('temp/'.$id, 'permanent/'.$id);
Storage::disk('s3')->move('processing/'.$id, 'done/'.$id);

// Filesystem config (config/filesystems.php)
return [
    'disks' => [
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false, // Return false on failure
        ],

        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
        ],
    ],
];
```

---

## 7.10 Exercises

1. Create a queued job with batching and chaining
2. Build a Markdown mail template with attachments
3. Implement a notification with 3 channels (mail, database, Slack)
4. Write an Artisan command with progress bar and options
5. Create a policy with before/after checks and use it in a controller

---

## 7.11 Interview Questions

1. "How do you handle failed jobs? What's the retry and backoff strategy?"
2. "Explain the notification system: how does `via()` determine channels?"
3. "What is the difference between events and listeners vs observsers?"
4. "How do Gates differ from Policies? When would you use each?"
5. "What is the `withoutOverlapping` middleware used for in scheduled tasks?"

---

## Further Reading

- **Doc:** [Laravel Queues](https://laravel.com/docs/queues)
- **Doc:** [Laravel Mail](https://laravel.com/docs/mail)
- **Doc:** [Laravel Notifications](https://laravel.com/docs/notifications)
- **Doc:** [Laravel Events](https://laravel.com/docs/events)
- **Doc:** [Laravel Authorization](https://laravel.com/docs/authorization)

---

*End of Chapter 7. Proceed to Chapter 8: Laravel Testing.*
