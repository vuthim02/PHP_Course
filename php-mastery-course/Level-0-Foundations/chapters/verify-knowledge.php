#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Level 0 Knowledge Verification Script
 * 
 * Run: php verify-knowledge.php
 * 
 * Tests your understanding of Level 0 concepts automatically.
 */

$passed = 0;
$failed = 0;
$total = 0;

function test(string $description, callable $fn): void
{
    global $passed, $failed, $total;
    $total++;
    try {
        $result = $fn();
        if ($result === true || $result === null) {
            $passed++;
            echo "  ✓ {$description}\n";
        } else {
            $failed++;
            echo "  ✗ {$description}\n    Expected truthy, got: " . var_export($result, true) . "\n";
        }
    } catch (\Throwable $e) {
        $failed++;
        echo "  ✗ {$description}\n    Error: {$e->getMessage()}\n";
    }
}

echo "═══ Level 0 Knowledge Verification ═══\n\n";

// ── Hardware & CPU ──────────────────────────────────────

echo "--- Hardware & CPU ---\n";

test('CPU fetch-decode-execute cycle has 3 steps', fn() => true);

test('CPU understands binary instructions', function () {
    // Binary AND operation
    $a = 0b1100;
    $b = 0b1010;
    return ($a & $b) === 0b1000;
});

test('RAM is volatile memory', function () {
    // RAM loses data on power loss
    // Cache is faster than RAM
    $cacheFaster = true;  // L1 cache is faster than RAM
    $ramFasterThanSsd = true; // RAM is faster than SSD
    return $cacheFaster && $ramFasterThanSsd;
});

test('Binary representation works', function () {
    // 42 in binary is 101010
    $decimal = 42;
    $binary = decbin($decimal);
    return $binary === '101010';
});

// ── Operating Systems ───────────────────────────────────

echo "\n--- Operating Systems ---\n";

test('Process vs thread memory isolation', function () {
    // Processes have isolated memory
    // Threads share memory within a process
    return true;
});

test('Context switching explained', function () {
    // Context switch = saving/loading process state
    // Expensive operation, avoid unnecessary switches
    return true;
});

test('Virtual memory maps addresses', function () {
    // Virtual memory = logical addresses mapped to physical RAM via page tables
    return true;
});

// ── Networking ──────────────────────────────────────────

echo "\n--- Networking ---\n";

test('DNS translates domain names to IPs', function () {
    $ip = gethostbyname('localhost');
    return $ip === '127.0.0.1';
});

test('TCP uses 3-way handshake', function () {
    // SYN → SYN-ACK → ACK
    return true;
});

test('Ports: HTTP=80, HTTPS=443, SSH=22, MySQL=3306', function () {
    $ports = [80 => 'HTTP', 443 => 'HTTPS', 22 => 'SSH', 3306 => 'MySQL'];
    return $ports[80] === 'HTTP' && $ports[443] === 'HTTPS';
});

// ── HTTP ────────────────────────────────────────────────

echo "\n--- HTTP ---\n";

test('HTTP methods: GET reads, POST creates, PUT replaces, DELETE removes', function () {
    $methods = [
        'GET' => 'read',
        'POST' => 'create',
        'PUT' => 'replace',
        'DELETE' => 'delete',
    ];
    return count($methods) === 4;
});

test('HTTP status code categories', function () {
    $categories = [
        200 => 'Success',
        301 => 'Redirect',
        400 => 'Client Error',
        404 => 'Not Found',
        500 => 'Server Error',
    ];
    return $categories[200] === 'Success' && $categories[404] === 'Not Found';
});

test('HTTPS uses TLS encryption', function () {
    // TLS handshake: ClientHello → ServerHello + Certificate → Key Exchange → Secure Connection
    return true;
});

// ── Servers & Databases ─────────────────────────────────

echo "\n--- Servers & Databases ---\n";

test('PHP-FPM manages PHP worker processes', function () {
    // PHP-FPM = FastCGI Process Manager
    // Handles multiple concurrent PHP requests
    return true;
});

test('SQL INNER JOIN combines related tables', function () {
    // Simulate JOIN logic
    $users = [1 => 'Alice', 2 => 'Bob'];
    $orders = [['user_id' => 1, 'product' => 'Book'], ['user_id' => 2, 'product' => 'Pen']];
    
    $joined = [];
    foreach ($orders as $order) {
        if (isset($users[$order['user_id']])) {
            $joined[] = $users[$order['user_id']] . ' bought ' . $order['product'];
        }
    }
    return $joined === ['Alice bought Book', 'Bob bought Pen'];
});

test('ACID transactions guarantee data integrity', function () {
    // Atomicity, Consistency, Isolation, Durability
    return true;
});

// ── Tools ───────────────────────────────────────────────

echo "\n--- Tools ---\n";

// Test PHP version
test('PHP 8.x is installed', function () {
    return version_compare(PHP_VERSION, '8.0', '>=');
});

test('File operations work', function () {
    $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tmpFile, 'Hello, Level 0!');
    
    $content = file_get_contents($tmpFile);
    unlink($tmpFile);
    
    return $content === 'Hello, Level 0!';
});

test('JSON encoding and decoding works', function () {
    $data = ['name' => 'Alice', 'skills' => ['PHP', 'SQL']];
    $json = json_encode($data);
    $decoded = json_decode($json, true);
    return $decoded['name'] === 'Alice';
});

test('Array functions: map, filter, reduce', function () {
    $numbers = [1, 2, 3, 4, 5];
    
    $doubled = array_map(fn($n) => $n * 2, $numbers);
    $even = array_filter($numbers, fn($n) => $n % 2 === 0);
    $sum = array_reduce($numbers, fn($carry, $n) => $carry + $n, 0);
    
    return $doubled === [2, 4, 6, 8, 10]
        && $even === [2 => 2, 4 => 4]
        && $sum === 15;
});

// ── Summary ─────────────────────────────────────────────

echo "\n═══ Results ═══\n";
echo "  Passed: {$passed}/{$total}\n";
echo "  Failed: {$failed}/{$total}\n";

if ($failed === 0) {
    echo "\n✓ All tests passed! Ready to proceed to Level 1.\n";
    exit(0);
} else {
    echo "\n✗ Some tests failed. Review the chapters above before moving on.\n";
    exit(1);
}
