<?php

declare(strict_types=1);

namespace UrlShortener\Service;

use Predis\ClientInterface;

/**
 * Redis-backed Bloom Filter for memory-efficient alias existence checks.
 *
 * Uses multiple hash functions (via double hashing technique) to set/check
 * bits in a Redis bitmap. False positives are possible but false negatives
 * are not — ideal for pre-checking alias collisions before querying MySQL.
 */
final class BloomFilter
{
    private const string BITMAP_KEY = 'bloom:aliases';
    private const int BITMAP_SIZE = 1_000_000; // ~125KB
    private const int NUM_HASH_FUNCTIONS = 7;

    public function __construct(
        private readonly ClientInterface $redis
    ) {}

    public function add(string $item): void
    {
        foreach ($this->getHashes($item) as $index) {
            $this->redis->setbit(self::BITMAP_KEY, $index, 1);
        }
    }

    public function mightExist(string $item): bool
    {
        foreach ($this->getHashes($item) as $index) {
            if ($this->redis->getbit(self::BITMAP_KEY, $index) === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate hash positions using Kirsch-Mitzenmacker optimization:
     * h_i(x) = h1(x) + i * h2(x)  (mod bitmap_size)
     */
    private function getHashes(string $item): array
    {
        $hash1 = crc32($item) & 0x7FFFFFFF;
        $hash2 = hexdec(substr(sha1($item), 0, 8)) & 0x7FFFFFFF;

        $hashes = [];
        for ($i = 0; $i < self::NUM_HASH_FUNCTIONS; $i++) {
            $hashes[] = ($hash1 + $i * $hash2) % self::BITMAP_SIZE;
        }
        return $hashes;
    }
}
