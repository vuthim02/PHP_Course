<?php

declare(strict_types=1);

namespace UserService\Services;

use UserService\Models\User;
use UserService\Models\Profile;

/**
 * UserService
 *
 * Core business logic for user management.
 * Encapsulates database access and Redis caching for user data.
 */
final class UserService
{
    private const CACHE_PREFIX = 'user:';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private \PDO $pdo,
        private \Redis $redis
    ) {
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        $id = bin2hex(random_bytes(16));
        $now = date('c');

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (id, email, password_hash, name, created_at, updated_at)
             VALUES (:id, :email, :password_hash, :name, :created_at, :updated_at)'
        );

        $stmt->execute([
            'id' => $id,
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name' => $data['name'] ?? '',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create empty profile
        $this->pdo->prepare(
            'INSERT INTO profiles (user_id) VALUES (:user_id)'
        )->execute(['user_id' => $id]);

        $user = new User($id, $data['email'], $data['name'] ?? '', '', $now);
        $this->cacheUser($user);

        return $user;
    }

    /**
     * Get a user by ID (with Redis caching).
     */
    public function get(string $id): ?User
    {
        // Check cache first
        $cached = $this->redis->get(self::CACHE_PREFIX . $id);
        if ($cached !== false) {
            return User::fromRow(json_decode($cached, true));
        }

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $user = User::fromRow($row);
        $this->cacheUser($user);

        return $user;
    }

    /**
     * List users with pagination.
     */
    public function list(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare(
            'SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $users = array_map(fn($row) => User::fromRow($row)->toPublicArray(), $stmt->fetchAll());

        $count = $this->count();

        return [
            'data' => $users,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $count,
                'total_pages' => (int) ceil($count / $perPage),
            ],
        ];
    }

    /**
     * Update a user.
     */
    public function update(string $id, array $data): User
    {
        $user = $this->get($id);
        if ($user === null) {
            throw new \RuntimeException("User {$id} not found");
        }

        $fields = [];
        $params = ['id' => $id];

        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params['name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params['email'] = $data['email'];
        }

        if (!empty($fields)) {
            $fields[] = 'updated_at = :updated_at';
            $params['updated_at'] = date('c');

            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $this->pdo->prepare($sql)->execute($params);
        }

        // Invalidate cache
        $this->redis->del(self::CACHE_PREFIX . $id);

        return $this->get($id);
    }

    /**
     * Delete a user.
     */
    public function delete(string $id): void
    {
        $this->pdo->prepare('DELETE FROM profiles WHERE user_id = :id')->execute(['id' => $id]);
        $this->pdo->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $id]);
        $this->redis->del(self::CACHE_PREFIX . $id);
    }

    /**
     * Get user profile.
     */
    public function getProfile(string $userId): ?array
    {
        $user = $this->get($userId);
        if ($user === null) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM profiles WHERE user_id = :id');
        $stmt->execute(['id' => $userId]);
        $profileRow = $stmt->fetch();

        $profile = $profileRow ? Profile::fromRow($profileRow) : new Profile($userId);

        return array_merge($user->toPublicArray(), $profile->toArray());
    }

    /**
     * Update user profile.
     */
    public function updateProfile(string $userId, array $data): array
    {
        $fields = [];
        $params = ['user_id' => $userId];

        foreach (['bio', 'avatar_url', 'company', 'location', 'website'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (isset($data['social_links'])) {
            $fields[] = 'social_links = :social_links';
            $params['social_links'] = json_encode($data['social_links']);
        }

        if (!empty($fields)) {
            $sql = 'UPDATE profiles SET ' . implode(', ', $fields) . ' WHERE user_id = :user_id';
            $this->pdo->prepare($sql)->execute($params);
        }

        return $this->getProfile($userId);
    }

    /**
     * Count total users.
     */
    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    /**
     * Cache a user in Redis.
     */
    private function cacheUser(User $user): void
    {
        $this->redis->setex(
            self::CACHE_PREFIX . $user->id,
            self::CACHE_TTL,
            json_encode($user->toArray())
        );
    }
}
