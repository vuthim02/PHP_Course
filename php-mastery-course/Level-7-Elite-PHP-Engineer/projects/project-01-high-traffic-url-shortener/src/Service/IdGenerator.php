<?php

declare(strict_types=1);

namespace UrlShortener\Service;

final class IdGenerator
{
    private const string BASE62_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const string BASE64_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';

    // Snowflake-inspired constants
    private const int EPOCH = 1700000000000; // Custom epoch (ms)
    private const int WORKER_ID_BITS = 5;
    private const int SEQUENCE_BITS = 12;

    private int $lastTimestamp = -1;
    private int $sequence = 0;

    public function __construct(
        private readonly int $workerId = 1
    ) {}

    /**
     * Generate a Snowflake-style 64-bit unique ID.
     * Format: [1 sign bit][41 timestamp ms][5 worker][12 sequence]
     */
    public function generateId(): int
    {
        $timestamp = $this->currentTimestamp();

        if ($timestamp < $this->lastTimestamp) {
            throw new \RuntimeException('Clock moved backwards');
        }

        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & 0xFFF;
            if ($this->sequence === 0) {
                $timestamp = $this->waitNextMillis();
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        return (($timestamp - self::EPOCH) << (self::WORKER_ID_BITS + self::SEQUENCE_BITS))
            | ($this->workerId << self::SEQUENCE_BITS)
            | $this->sequence;
    }

    public function encodeBase62(int $id): string
    {
        $result = '';
        $num = $id;
        $base = strlen(self::BASE62_ALPHABET);

        while ($num > 0) {
            $result = self::BASE62_ALPHABET[$num % $base] . $result;
            $num = intdiv($num, $base);
        }

        return $result ?: self::BASE62_ALPHABET[0];
    }

    public function encodeBase64(int $id): string
    {
        $result = '';
        $num = $id;
        $base = strlen(self::BASE64_ALPHABET);

        while ($num > 0) {
            $result = self::BASE64_ALPHABET[$num % $base] . $result;
            $num = intdiv($num, $base);
        }

        return $result ?: self::BASE64_ALPHABET[0];
    }

    public function decodeBase62(string $code): int
    {
        $result = 0;
        $base = strlen(self::BASE62_ALPHABET);

        for ($i = 0, $len = strlen($code); $i < $len; $i++) {
            $result = $result * $base + strpos(self::BASE62_ALPHABET, $code[$i]);
        }

        return $result;
    }

    private function currentTimestamp(): int
    {
        return (int) (microtime(true) * 1000);
    }

    private function waitNextMillis(): int
    {
        $timestamp = $this->currentTimestamp();
        while ($timestamp <= $this->lastTimestamp) {
            usleep(100);
            $timestamp = $this->currentTimestamp();
        }
        return $timestamp;
    }
}
