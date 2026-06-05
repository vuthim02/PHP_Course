<?php

declare(strict_types=1);

namespace UrlShortener\Service;

use UrlShortener\Cache\CacheInterface;
use UrlShortener\Repository\UrlRepository;

final class UrlService
{
    private const int CACHE_TTL = 86400; // 24 hours
    private const int CUSTOM_ALIAS_MIN_LENGTH = 4;

    public function __construct(
        private readonly UrlRepository $repository,
        private readonly CacheInterface $cache,
        private readonly IdGenerator $idGenerator,
        private readonly BloomFilter $bloomFilter
    ) {}

    public function shorten(string $originalUrl, ?string $customAlias = null): array
    {
        $this->validateUrl($originalUrl);

        if ($customAlias !== null) {
            $this->validateCustomAlias($customAlias);
            if ($this->aliasExists($customAlias)) {
                throw new \InvalidArgumentException('Custom alias already in use');
            }
        }

        $shortCode = $customAlias ?? $this->idGenerator->encodeBase62(
            $this->idGenerator->generateId()
        );

        $this->repository->save([
            'short_code' => $shortCode,
            'original_url' => $originalUrl,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->cache->set("code:{$shortCode}", $originalUrl, self::CACHE_TTL);
        $this->bloomFilter->add($shortCode);

        return [
            'short_code' => $shortCode,
            'short_url' => $_ENV['BASE_URL'] . '/' . $shortCode,
            'original_url' => $originalUrl,
        ];
    }

    public function resolve(string $shortCode): ?string
    {
        $cacheKey = "code:{$shortCode}";

        $url = $this->cache->get($cacheKey);
        if ($url !== null) {
            return $url;
        }

        $record = $this->repository->findByCode($shortCode);
        if ($record === null) {
            return null;
        }

        $this->cache->set($cacheKey, $record['original_url'], self::CACHE_TTL);

        return $record['original_url'];
    }

    public function aliasExists(string $alias): bool
    {
        if ($this->bloomFilter->mightExist($alias)) {
            return $this->repository->findByCode($alias) !== null;
        }
        return false;
    }

    private function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }

        if (strlen($url) > 2048) {
            throw new \InvalidArgumentException('URL exceeds maximum length of 2048 characters');
        }
    }

    private function validateCustomAlias(string $alias): void
    {
        if (strlen($alias) < self::CUSTOM_ALIAS_MIN_LENGTH) {
            throw new \InvalidArgumentException(
                "Custom alias must be at least " . self::CUSTOM_ALIAS_MIN_LENGTH . " characters"
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $alias)) {
            throw new \InvalidArgumentException(
                'Custom alias can only contain alphanumeric characters, hyphens, and underscores'
            );
        }
    }
}
