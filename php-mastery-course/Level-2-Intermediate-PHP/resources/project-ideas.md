# Level 2 — Project Ideas

## Project 1: Blog Engine with Admin Panel

Build a complete blog application with:
- User registration/login with sessions
- Role-based access (admin, author, reader)
- CRUD for posts (create, edit, delete, publish)
- Categories and tags (many-to-many relationships)
- Comments with moderation
- Search functionality
- Pagination
- File upload for post images
- Simple admin dashboard

**Techniques to practice:** OOP, MVC pattern, PDO, authentication, file uploads, pagination, prepared statements, sessions.

---

## Project 2: Task Management System (Trello Clone Lite)

A kanban-style task manager:
- Projects with multiple boards
- Cards that move between columns (To Do, In Progress, Done)
- Drag-and-drop reordering (can use JavaScript sortable library)
- Task assignments to users
- Due dates with notifications
- Comments on cards
- Labels/tags with colors
- Activity log (who did what and when)

**Techniques to practice:** OOP, database relationships, MVC, session management, AJAX integration, SQL JOINs.

---

## Project 3: E-Commerce Shopping Cart

A fully functional online store:
- Product catalog with categories
- Product search with filters
- Shopping cart (stored in session)
- Checkout process with address form
- Order history per user
- Admin panel for inventory management
- Stock tracking
- Simple discount coupon system

**Techniques to practice:** Sessions, cart management, database transactions, form validation, MVC, file uploads (product images).

---

## Project 4: URL Shortener (like bit.ly)

A service that shortens URLs:
- User can submit a long URL
- Generate unique short code
- Redirect short URLs to original
- Track click counts per link
- User accounts to manage links
- Link expiration option
- QR code generation for each link
- Simple analytics (clicks per day, referrers)

**Techniques to practice:** URL routing, redirects, unique ID generation, tracking/analytics, database queries, caching.

---

## Project 5: Real-Time Chat Application

A simple chat system:
- User registration/login
- Create chat rooms
- Send messages in rooms
- Messages stored in database
- Poll for new messages (AJAX)
- Typing indicators
- Online/offline status
- Message history with pagination

**Techniques to practice:** AJAX polling, JSON responses, database queries, sessions, timestamp handling.

---

## Building Tips

For any of these projects, follow this approach:

1. **Plan first:** Draw the database schema on paper
2. **Start small:** Get one feature working end-to-end
3. **Use MVC:** Separate models, views, controllers
4. **Refactor:** After a feature works, clean up the code
5. **Add tests:** Write PHPUnit tests for models and controllers
6. **Polish:** Add validation, error handling, and security
7. **Deploy:** Put it on a free hosting platform (Heroku, Railway, etc.)

### Database Schema Template

```sql
-- Example for Blog Engine
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'author', 'reader') DEFAULT 'reader',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    body TEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE post_category (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT,
    author_name VARCHAR(100),
    body TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```
