# Chapter 20: Project: Laravel SaaS Application

## Project Overview

Build a complete SaaS application with Laravel featuring multi-tenancy, subscription billing, and team management.

---

## 20.1 Requirements

### Features
- Multi-tenancy (each team has isolated data)
- Subscription management (Stripe)
- Team member invitations
- Role-based access within teams
- Usage billing (per-seat or per-feature)
- Admin dashboard
- API for frontend/third-party

### Technical Stack
- Laravel 11
- Laravel Cashier (Stripe)
- Laravel Jetstream (team management)
- Spatie/laravel-permission (RBAC)
- MySQL with tenant isolation
- Redis for caching and queues

---

## 20.2 Key Architecture

```php
<?php
// Tenant isolation via database
class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving(Repository::class, function ($repository, $app) {
            if ($tenantId = session('tenant_id')) {
                $repository->setTenantId($tenantId);
            }
        });
    }
}

// Subscription management
class SubscriptionController extends Controller
{
    public function subscribe(SubscriptionRequest $request): RedirectResponse
    {
        $plan = Plan::findOrFail($request->plan_id);
        
        $subscription = $request->user()->newSubscription(
            $plan->name,
            $plan->stripe_price_id
        )->create($request->payment_method_id);

        Tenant::forCurrent()->update([
            'plan_id' => $plan->id,
            'subscription_status' => 'active',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Subscribed to ' . $plan->name);
    }

    public function cancel(): RedirectResponse
    {
        auth()->user()->subscription()->cancel();
        
        Tenant::forCurrent()->update([
            'subscription_status' => 'cancelled',
        ]);

        return back()->with('success', 'Subscription cancelled');
    }
}

// Team management
class TeamInvitationController extends Controller
{
    public function invite(InviteRequest $request): RedirectResponse
    {
        $team = auth()->user()->currentTeam;

        if ($team->users->count() >= $team->plan->max_users) {
            return back()->withErrors(['team' => 'Team member limit reached']);
        }

        $invitation = $team->invitations()->create([
            'email' => $request->email,
            'role' => $request->role,
        ]);

        $invitation->sendEmailNotification();

        return back()->with('success', 'Invitation sent!');
    }
}
```

---

## 20.3 Deliverables

1. Multi-tenant SaaS application
2. Stripe subscription billing
3. Team management with invitations
4. Role-based permissions
5. Admin dashboard
6. RESTful API
7. Automated tests

---

## Further Reading

- **Doc:** [Laravel Cashier](https://laravel.com/docs/11.x/billing)
- **Doc:** [Laravel Jetstream](https://jetstream.laravel.com/)
- **Doc:** [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
