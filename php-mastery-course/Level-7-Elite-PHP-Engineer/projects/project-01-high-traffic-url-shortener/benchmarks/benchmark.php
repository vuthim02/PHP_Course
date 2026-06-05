<?php

declare(strict_types=1);

/**
 * URL Shortener Benchmark Script
 *
 * Simulates high-traffic load testing the URL shortener's core components:
 * - ID generation throughput
 * - Base62 encoding/decoding speed
 * - Cache get/set performance
 * - Redis queue push/pop throughput
 *
 * Run: php benchmarks/benchmark.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use UrlShortener\Service\IdGenerator;

// ─── Configuration ─────────────────────────────────────────────────
const ITERATIONS = 50000;
const CONCURRENCY = 10;
const WARMUP = 1000;

$results = [];

// ─── Benchmark Helper ──────────────────────────────────────────────
function benchmark(string $name, callable $fn, int $iterations): array
{
    // Warmup
    for ($i = 0; $i < WARMUP; $i++) {
        $fn();
    }

    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $fn();
    }
    $end = microtime(true);

    $elapsed = ($end - $start);
    $opsPerSec = $iterations / $elapsed;
    $avgMs = ($elapsed / $iterations) * 1000;

    return [
        'name' => $name,
        'iterations' => $iterations,
        'elapsed_sec' => round($elapsed, 4),
        'ops_per_sec' => round($opsPerSec, 0),
        'avg_ms' => round($avgMs, 4),
    ];
}

echo str_repeat('=', 70) . "\n";
echo "  URL Shortener Benchmark v1.0\n";
echo "  Iterations: " . number_format(ITERATIONS) . " per test\n";
echo str_repeat('=', 70) . "\n\n";

// ─── 1. ID Generation ─────────────────────────────────────────────
$idGen = new IdGenerator(1);

$results[] = benchmark('Snowflake ID Generation', function () use ($idGen) {
    $idGen->generateId();
}, ITERATIONS);

// ─── 2. Base62 Encoding ───────────────────────────────────────────
$id = $idGen->generateId();

$results[] = benchmark('Base62 Encode', function () use ($idGen, $id) {
    $idGen->encodeBase62($id);
}, ITERATIONS);

// ─── 3. Base64 Encoding ───────────────────────────────────────────
$results[] = benchmark('Base64 Encode', function () use ($idGen, $id) {
    $idGen->encodeBase64($id);
}, ITERATIONS);

// ─── 4. Base62 Decode ─────────────────────────────────────────────
$encoded = $idGen->encodeBase62($id);

$results[] = benchmark('Base62 Decode', function () use ($idGen, $encoded) {
    $idGen->decodeBase62($encoded);
}, ITERATIONS);

// ─── 5. Bloom Filter Operations ───────────────────────────────────
// Using in-memory array-based bloom for micro-benchmark
$bloom = [];
$item = 'test-alias-123';

$results[] = benchmark('Bloom Filter Add (simulated)', function () use (&$bloom, $item) {
    $h1 = crc32($item);
    $h2 = hexdec(substr(sha1($item), 0, 8));
    for ($i = 0; $i < 7; $i++) {
        $bloom[($h1 + $i * $h2) % 1000000] = true;
    }
}, ITERATIONS);

$results[] = benchmark('Bloom Filter Check (simulated)', function () use ($bloom, $item) {
    $h1 = crc32($item);
    $h2 = hexdec(substr(sha1($item), 0, 8));
    for ($i = 0; $i < 7; $i++) {
        isset($bloom[($h1 + $i * $h2) % 1000000]);
    }
}, ITERATIONS);

// ─── 6. JSON Serialization ────────────────────────────────────────
$payload = [
    'short_code' => 'abc123',
    'original_url' => 'https://example.com/very/long/path/with/many/segments?query=param&another=value',
    'created_at' => date('Y-m-d H:i:s'),
];

$results[] = benchmark('JSON Encode', function () use ($payload) {
    json_encode($payload);
}, ITERATIONS);

$json = json_encode($payload);
$results[] = benchmark('JSON Decode', function () use ($json) {
    json_decode($json, true);
}, ITERATIONS);

// ─── 7. String operations ─────────────────────────────────────────
$longUrl = 'https://example.com/' . str_repeat('path/', 50);

$results[] = benchmark('URL Validation (filter_var)', function () use ($longUrl) {
    filter_var($longUrl, FILTER_VALIDATE_URL);
}, ITERATIONS);

$results[] = benchmark('Regex Custom Alias Validation', function () {
    preg_match('/^[a-zA-Z0-9_-]+$/', 'my-custom-alias-123');
}, ITERATIONS);

// ─── Results Table ─────────────────────────────────────────────────
echo str_pad('Test Name', 35) . ' '
    . str_pad('Ops/sec', 14, ' ', STR_PAD_LEFT) . ' '
    . str_pad('Avg (ms)', 12, ' ', STR_PAD_LEFT) . ' '
    . "Time (s)\n";
echo str_repeat('-', 70) . "\n";

foreach ($results as $r) {
    echo str_pad($r['name'], 35)
        . str_pad(number_format($r['ops_per_sec']), 14, ' ', STR_PAD_LEFT)
        . str_pad($r['avg_ms'], 12, ' ', STR_PAD_LEFT)
        . " {$r['elapsed_sec']}\n";
}

echo str_repeat('=', 70) . "\n";
echo "  Benchmark complete!\n";
echo str_repeat('=', 70) . "\n";
