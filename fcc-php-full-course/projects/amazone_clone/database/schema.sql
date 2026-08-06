-- schema.sql — amazone_clone schema for SQLite (Phase 0)
-- Executed by database/setup.php — do not pipe this into the MySQL client.

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS return_items;
DROP TABLE IF EXISTS returns;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    name             TEXT NOT NULL,
    email            TEXT NOT NULL UNIQUE,
    password_hash    TEXT NOT NULL,
    is_admin         INTEGER NOT NULL DEFAULT 0,
    shipping_address TEXT NULL,
    city             TEXT NULL,
    zip              TEXT NULL,
    created_at       TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE categories (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    name      TEXT NOT NULL,
    slug      TEXT NOT NULL UNIQUE,
    parent_id INTEGER NULL REFERENCES categories(id) ON DELETE SET NULL,
    image_url TEXT NULL
);

CREATE TABLE products (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id  INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    name         TEXT NOT NULL,
    slug         TEXT NOT NULL UNIQUE,
    description  TEXT NULL,
    price        REAL NOT NULL,
    deal_price   REAL NULL,
    stock        INTEGER NOT NULL DEFAULT 0,
    image_url    TEXT NULL,
    rating_avg   REAL NOT NULL DEFAULT 0.0,
    rating_count INTEGER NOT NULL DEFAULT 0,
    created_at   TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_rating ON products(rating_count DESC);

CREATE TABLE reviews (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rating     INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    UNIQUE (user_id, product_id)
);

CREATE TABLE cart_items (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id       INTEGER NULL REFERENCES users(id) ON DELETE CASCADE,
    session_token TEXT NULL,
    product_id    INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity      INTEGER NOT NULL DEFAULT 1,
    added_at      TEXT NOT NULL DEFAULT (datetime('now')),
    UNIQUE (user_id, product_id),
    UNIQUE (session_token, product_id)
);

CREATE TABLE orders (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id          INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
    total            REAL NOT NULL,
    status           TEXT NOT NULL DEFAULT 'pending'
                     CHECK (status IN ('pending','paid','shipped','delivered','cancelled')),
    shipping_address TEXT NOT NULL,
    city             TEXT NOT NULL,
    zip              TEXT NOT NULL,
    created_at       TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE order_items (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id          INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id        INTEGER NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    price_at_purchase REAL NOT NULL,
    quantity          INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE returns (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id   INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reason     TEXT NULL,
    status     TEXT NOT NULL DEFAULT 'requested'
               CHECK (status IN ('requested','approved','rejected','received','refunded')),
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE return_items (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    return_id     INTEGER NOT NULL REFERENCES returns(id) ON DELETE CASCADE,
    order_item_id INTEGER NOT NULL REFERENCES order_items(id) ON DELETE CASCADE,
    quantity      INTEGER NOT NULL DEFAULT 1
);
