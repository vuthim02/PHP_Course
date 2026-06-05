-- Bookstore API Database Schema
-- Run: mysql -u root -p < migrations/init.sql

CREATE DATABASE IF NOT EXISTS bookstore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bookstore;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at INT UNSIGNED NOT NULL,
    updated_at INT UNSIGNED NOT NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Authors table
CREATE TABLE IF NOT EXISTS authors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    birth_date DATE NULL,
    created_at INT UNSIGNED NOT NULL,
    updated_at INT UNSIGNED NOT NULL,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at INT UNSIGNED NOT NULL,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    description TEXT,
    published_year INT UNSIGNED,
    cover_image VARCHAR(500),
    created_at INT UNSIGNED NOT NULL,
    updated_at INT UNSIGNED NOT NULL,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_title (title),
    INDEX idx_isbn (isbn),
    INDEX idx_price (price),
    FULLTEXT idx_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    updated_at INT UNSIGNED NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_book_id (book_id),
    INDEX idx_user_id (user_id),
    UNIQUE KEY unique_review (book_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX idx_key (key),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data
INSERT INTO categories (name, description, created_at) VALUES
    ('Fiction', 'Fictional literature and novels', UNIX_TIMESTAMP()),
    ('Non-Fiction', 'Non-fictional educational content', UNIX_TIMESTAMP()),
    ('Science', 'Scientific publications and research', UNIX_TIMESTAMP()),
    ('Technology', 'Technology and programming books', UNIX_TIMESTAMP()),
    ('History', 'Historical books and biographies', UNIX_TIMESTAMP()),
    ('Fantasy', 'Fantasy and sci-fi genre', UNIX_TIMESTAMP());

INSERT INTO authors (name, bio, created_at, updated_at) VALUES
    ('J.K. Rowling', 'British author, best known for the Harry Potter series', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('George Orwell', 'English novelist and essayist', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('Robert C. Martin', 'American software engineer and author', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
    ('Yuval Noah Harari', 'Israeli historian and professor', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
