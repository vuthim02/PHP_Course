<?php
// setup.php — CLI script that creates the SQLite database from schema + seed.
// Usage:  php database/setup.php
// Run it again anytime to reset the database to the sample data.

$dbFile = __DIR__ . "/amazone.sqlite";

if (file_exists($dbFile)) {
    unlink($dbFile);
}

$pdo = new PDO("sqlite:" . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("PRAGMA foreign_keys = ON");

$pdo->exec(file_get_contents(__DIR__ . "/schema.sql"));
$pdo->exec(file_get_contents(__DIR__ . "/seed.sql"));

$count = (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$cats  = (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

echo "Created " . $dbFile . "\n";
echo "  categories: {$cats}\n";
echo "  products:   {$count}\n";
