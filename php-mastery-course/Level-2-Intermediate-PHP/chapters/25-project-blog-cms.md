# Chapter 25: Project: Blog CMS from Scratch

## Project Overview

Build a complete Content Management System (CMS) with user authentication, post management, comments, and a public-facing blog.

---

## 25.1 Requirements

### Features
- User registration and login
- Role-based access (Admin, Editor, Author)
- Create, edit, delete posts (markdown supported)
- Comment system with moderation
- Categories and tags
- Image upload for posts
- Search functionality
- Pagination
- RSS feed

### Technical Stack
- Pure PHP (no framework)
- MySQL/MariaDB
- PDO for database access
- Bootstrap for basic styling
- Markdown parsing with Parsedown

---

## 25.2 Database Schema

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'author', 'subscriber') DEFAULT 'subscriber',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    featured_image VARCHAR(255),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FULLTEXT INDEX idx_search (title, content)
);

CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    author_name VARCHAR(100),
    author_email VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE post_tag (
    post_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

---

## 25.3 Architecture

```
blog-cms/
├── public/              # Web root
│   └── index.php        # Front controller
├── src/
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Data access
│   ├── Middleware/       # Auth, CSRF, etc.
│   ├── Services/        # Business logic
│   ├── Validators/      # Input validation
│   └── Helpers/         # Utility functions
├── views/
│   ├── layouts/         # Master layout
│   ├── auth/            # Login/register
│   ├── posts/           # Post CRUD
│   ├── admin/           # Admin dashboard
│   └── partials/        # Reusable components
├── config/
│   └── database.php     # DB configuration
├── migrations/          # Schema migrations
├── storage/             # Uploaded files
├── vendor/              # Composer packages
├── composer.json
└── .env
```

---

## 25.4 Implementation Tasks

1. **User Module**: Register, login, logout, password reset
2. **Post CRUD**: Create, read, update, delete posts
3. **Comments**: Add, approve, delete comments
4. **Admin Dashboard**: Manage users, posts, comments
5. **Public Blog**: List posts, view single post, search
6. **RSS Feed**: Generate blog RSS feed
7. **Image Upload**: Featured image for posts
8. **SEO**: Meta tags, sitemap, clean URLs

---

## 25.5 Deliverables

1. Complete working blog CMS
2. Admin panel for content management
3. Public blog with search and RSS
4. User authentication and authorization
5. SQL dump with sample data

---

## Further Reading

- **Doc:** [PHP The Right Way](https://phptherightway.com/)
