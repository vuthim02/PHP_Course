<?php

declare(strict_types=1);

namespace Models;

use MiniORM\Attributes\Column;
use MiniORM\Attributes\Id;
use MiniORM\Attributes\OneToMany;
use MiniORM\Attributes\Table;

#[Table('users')]
class User
{
    #[Id]
    #[Column('id', 'int')]
    private int $id;

    #[Column('username', 'string')]
    private string $username;

    #[Column('email', 'string')]
    private string $email;

    #[Column('created_at', 'datetime')]
    private ?string $createdAt = null;

    #[OneToMany(Post::class, 'user_id')]
    private array $posts = [];

    public function __construct(string $username = '', string $email = '')
    {
        $this->username = $username;
        $this->email    = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getPosts(): array
    {
        return $this->posts;
    }
}
