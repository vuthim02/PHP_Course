<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MiniORM\Connection;
use MiniORM\EntityManager;
use MiniORM\InMemoryStorage;
use Models\Post;
use Models\User;

echo "=== Mini ORM Demo (In-Memory) ===\n\n";

$connection = new Connection();
$em = new EntityManager($connection);

$storage = $connection->connect();

echo "Creating user via EntityManager...\n";
$user = new User('johndoe', 'john@example.com');
$em->persist($user);
$em->flush();
echo "User created with ID: " . $user->getId() . "\n\n";

echo "Creating posts...\n";
foreach (['First Post', 'Second Post', 'Third Post'] as $i => $title) {
    $post = new Post($title, "Body of $title", $user->getId());
    $em->persist($post);
    $em->flush();
    echo "  Post #{$post->getId()}: {$post->getTitle()}\n";
}
echo "\n";

echo "Finding user by ID...\n";
$found = $em->find(User::class, $user->getId());
if ($found !== null) {
    echo "  Found: {$found->getUsername()} ({$found->getEmail()})\n\n";
}

echo "Using Repository to find all users...\n";
$repo = $em->repository(User::class);
$users = $repo->findAll();
foreach ($users as $u) {
    echo "  - {$u->getUsername()} (ID: {$u->getId()})\n";
}
echo "\n";

echo "Using QueryBuilder for custom queries...\n";
$qb = new \MiniORM\QueryBuilder($connection);
$results = $qb->table('posts')
    ->where('user_id', '=', $user->getId())
    ->orderBy('created_at', 'DESC')
    ->get();
echo "  Found " . count($results) . " posts for user {$user->getUsername()}\n";
foreach ($results as $row) {
    echo "  - {$row['title']}\n";
}
echo "\n";

echo "Repository count: " . $repo->count() . " users\n\n";

echo "Generating migration SQL...\n";
$migration = $em->createMigration(User::class);
echo $migration . "\n\n";

echo "Updating user email...\n";
$user->setEmail('john.doe@example.com');
$em->save($user);
echo "  Email updated to: {$user->getEmail()}\n\n";

// Refresh and verify
$fresh = $em->find(User::class, $user->getId());
echo "Verified user email: {$fresh->getEmail()}\n\n";

echo "Demo completed successfully!\n";
