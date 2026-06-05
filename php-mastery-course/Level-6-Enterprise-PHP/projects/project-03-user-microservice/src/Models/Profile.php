<?php

declare(strict_types=1);

namespace UserService\Models;

/**
 * Profile Model
 *
 * User profile with additional metadata.
 * Demonstrates a related entity in a microservice context.
 */
final class Profile
{
    public function __construct(
        public readonly string $userId,
        public ?string $bio = null,
        public ?string $avatarUrl = null,
        public ?string $company = null,
        public ?string $location = null,
        public ?string $website = null,
        public ?array $socialLinks = null
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            $row['user_id'],
            $row['bio'] ?? null,
            $row['avatar_url'] ?? null,
            $row['company'] ?? null,
            $row['location'] ?? null,
            $row['website'] ?? null,
            isset($row['social_links']) ? json_decode($row['social_links'], true) : null
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'bio' => $this->bio,
            'avatar_url' => $this->avatarUrl,
            'company' => $this->company,
            'location' => $this->location,
            'website' => $this->website,
            'social_links' => $this->socialLinks,
        ];
    }
}
