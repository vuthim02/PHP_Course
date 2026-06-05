# Chapter 11: Symfony Advanced

## Learning Objectives

- Master Doctrine ORM and DBAL
- Implement Messenger for async processing
- Use Workflow component
- Build REST APIs with API Platform

---

## 11.1 Messenger Component

```php
<?php
// Message
class SendWelcomeEmail implements AsyncMessage
{
    public function __construct(
        public int $userId,
        public string $email
    ) {}
}

// Handler
class SendWelcomeEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $users
    ) {}

    public function __invoke(SendWelcomeEmail $message): void
    {
        $user = $this->users->find($message->userId);
        
        $email = (new TemplatedEmail())
            ->from('noreply@example.com')
            ->to($message->email)
            ->subject('Welcome!')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }
}

// Dispatch
$this->bus->dispatch(new SendWelcomeEmail($user->getId(), $user->getEmail()));

// config/packages/messenger.yaml
// framework:
//     messenger:
//         transports:
//             async: '%env(MESSENGER_TRANSPORT_DSN)%'
//         routing:
//             'App\Message\SendWelcomeEmail': async

// Workflow
$definition = new Definition(
    ['draft', 'review', 'approved', 'published'],
    ['draft_to_review' => ['from' => 'draft', 'to' => 'review'],
     'review_to_approved' => ['from' => 'review', 'to' => 'approved'],
     'review_to_rejected' => ['from' => 'review', 'to' => 'draft'],
     'approved_to_published' => ['from' => 'approved', 'to' => 'published']]
);

$workflow = new Workflow($definition);
$post = new Post();
$workflow->can($post, 'draft_to_review'); // true
$workflow->apply($post, 'draft_to_review');
```

---

## 11.2 Exercises

1. Build a product import pipeline using Messenger
2. Create a blog post workflow with transitions
3. Use Serializer for complex data transformations
4. Integrate API Platform for a REST API

---

## Further Reading

- **Doc:** [Symfony Messenger](https://symfony.com/doc/current/messenger.html)
- **Doc:** [API Platform](https://api-platform.com/)
