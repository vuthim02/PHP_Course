<?php

declare(strict_types=1);

namespace Models;

use MiniORM\Attributes\BelongsTo;
use MiniORM\Attributes\Column;
use MiniORM\Attributes\Id;
use MiniORM\Attributes\Table;

#[Table('posts')]
class Post
{
    #[Id]
    #[Column('id', 'int')]
    private int $id;

    #[Column('title', 'string')]
    private string $title;

    #[Column('body', 'text')]
    private string $body;

    #[Column('user_id', 'int')]
    private int $userId;

    #[Column('created_at', 'datetime')]
    private ?string $createdAt = null;

    #[BelongsTo(User::class, 'user_id')]
    private ?User $user = null;

    public function __construct(string $title = '', string $body = '', int $userId = 0)
    {
        $this->title  = $title;
        $this->body   = $body;
        $this->userId = $userId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
