# Mini ORM with Reflection & Attributes

A lightweight ORM leveraging PHP 8 attributes for entity mapping. Supports CRUD operations, relationship metadata, query building, and migration generation — all powered by Reflection API.

## Advanced PHP Concepts Demonstrated

| Concept | File |
|---|---|
| **PHP 8 Attributes** | `src/ORM/Attributes/*.php` — Custom `#[Table]`, `#[Column]`, `#[Id]`, `#[OneToMany]`, `#[BelongsTo]` |
| **Reflection API** | `src/ORM/EntityManager.php:101-131` — Reads attributes, hydrates objects, introspects properties |
| **Late Static Binding** | `src/ORM/QueryBuilder.php` — `static` return type for fluent interface |
| **Type System Deep Dive** | `src/Models/*.php` — Typed properties, union types (`?string`), constructor promotion |
| **Anonymous Functions** | `src/ORM/EntityManager.php:166` — `array_map` with closure for hydration |
| **Named Arguments** | `src/ORM/Attributes/*.php` — Attribute constructors use named arguments |

## Features

- **PHP 8 Attributes** for ORM mapping (`#[Table]`, `#[Column]`, `#[Id]`, `#[OneToMany]`, `#[BelongsTo]`)
- **EntityManager** with `persist()`, `flush()`, `save()`, `find()`, `findAll()`
- **Repository** pattern with `findBy()`, `findOneBy()`, `count()`, `delete()`
- **Fluent QueryBuilder** with `where()`, `orderBy()`, `limit()`, `join()`
- **Migration Generator** — auto-generates `CREATE TABLE` SQL from entity attributes
- **Hydration** — automatically maps database rows to typed entity objects using Reflection

## Setup

```bash
composer install
```

## Run Demo

```bash
php examples/crud.php
```

## Usage

```php
<?php

use MiniORM\Connection;
use MiniORM\EntityManager;
use Models\User;

// Define entity with attributes
#[Table('users')]
class User {
    #[Id]
    #[Column('id', 'int')]
    private int $id;

    #[Column('username', 'string')]
    private string $username;
}

// Use the ORM
$em = new EntityManager(new Connection('sqlite:path/to/db.sqlite'));
$user = new User('alice', 'alice@example.com');

$em->persist($user);
$em->flush();

// Find by ID
$found = $em->find(User::class, 1);

// QueryBuilder
$qb = $em->repository(User::class)->query();
$results = $qb->where('username', '=', 'alice')->get();

// Generate migration
$sql = $em->createMigration(User::class);
```

## What Makes It "Advanced PHP"

This project demonstrates a complete attribute-driven ORM architecture using PHP 8's native **attribute system** combined with **Reflection API** for metadata extraction and hydration. The **fluent QueryBuilder** uses `static` return types for proper inheritance support via **late static binding**. **Typed properties** guarantee type safety throughout. This is the architectural pattern used by modern ORMs like Doctrine, implemented from scratch.
