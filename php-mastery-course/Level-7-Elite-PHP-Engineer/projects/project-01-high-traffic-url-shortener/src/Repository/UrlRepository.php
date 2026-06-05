<?php

declare(strict_types=1);

namespace UrlShortener\Repository;

final class UrlRepository
{
    public function __construct(
        private readonly \PDO $primary,
        private readonly array $replicas = []
    ) {}

    public function save(array $data): void
    {
        $stmt = $this->primary->prepare(
            'INSERT INTO urls (short_code, original_url, created_at)
             VALUES (:short_code, :original_url, :created_at)'
        );

        $stmt->execute([
            'short_code' => $data['short_code'],
            'original_url' => $data['original_url'],
            'created_at' => $data['created_at'],
        ]);
    }

    public function findByCode(string $shortCode): ?array
    {
        $pdo = $this->getReadConnection();

        $stmt = $pdo->prepare(
            'SELECT id, short_code, original_url, created_at
             FROM urls WHERE short_code = :short_code LIMIT 1'
        );
        $stmt->execute(['short_code' => $shortCode]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findByOriginalUrl(string $url): ?array
    {
        $pdo = $this->getReadConnection();

        $stmt = $pdo->prepare(
            'SELECT id, short_code, original_url, created_at
             FROM urls WHERE original_url = :url LIMIT 1'
        );
        $stmt->execute(['url' => $url]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function delete(string $shortCode): void
    {
        $stmt = $this->primary->prepare(
            'DELETE FROM urls WHERE short_code = :short_code'
        );
        $stmt->execute(['short_code' => $shortCode]);
    }

    private function getReadConnection(): \PDO
    {
        if (empty($this->replicas)) {
            return $this->primary;
        }

        return $this->replicas[array_rand($this->replicas)];
    }
}
