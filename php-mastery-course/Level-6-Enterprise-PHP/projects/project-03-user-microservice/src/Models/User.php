<?php

declare(strict_types=1);

namespace UserService\Models;

/**
 * User Model
 *
 * Represents a user entity in the system.
 * Simple active-record-adjacent model for a microservice context.
 */
final class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public string $name,
        public readonly string $passwordHash,
        public readonly string $createdAt,
        public ?string $updatedAt = null,
        public ?string $lastLoginAt = null,
        public bool $isActive = true
    ) {
    }

    /**
     * Create a User from a database row.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            $row['id'],
            $row['email'],
            $row['name'],
            $row['password_hash'],
            $row['created_at'],
            $row['updated_at'] ?? null,
            $row['last_login_at'] ?? null,
            (bool) ($row['is_active'] ?? true)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'last_login_at' => $this->lastLoginAt,
            'is_active' => $this->isActive,
        ];
    }

    /**
     * Strip sensitive fields for API responses.
     */
    public function toPublicArray(): array
    {
        $data = $this->toArray();
        unset($data['password_hash']);
        return $data;
    }
}
