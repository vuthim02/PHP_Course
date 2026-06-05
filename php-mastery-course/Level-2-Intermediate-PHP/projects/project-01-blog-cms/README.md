# Project 1: Blog CMS with MySQL

A full-featured Content Management System for blogging built with PHP 8 and MySQL. Users can register, create/manage posts, comment, and admins have full control over the platform.

## Learning Objectives

- **OOP**: Classes, constructors, typed properties, inheritance, encapsulation
- **PDO & Prepared Statements**: SQL injection prevention, parameterized queries
- **MVC Architecture**: Separation of concerns (Models, Views, Controllers)
- **Namespaces & PSR-4**: Composer autoloading with App\ namespace
- **Authentication**: Password hashing (bcrypt), session management
- **Authorization**: Role-based access (user, editor, admin)
- **Exception Handling**: Try-catch for database operations
- **CRUD Operations**: Full create, read, update, delete for posts, comments, categories, users

## Features

- User registration and login with bcrypt password hashing
- Create, edit, delete, and publish blog posts
- Rich text content with excerpts
- Category management
- Comment system with approval workflow (admin)
- Pagination for post listing
- Admin dashboard with statistics
- User management (role assignment, deletion)
- Responsive CSS design

## Setup Instructions

```bash
# 1. Navigate to project directory
cd php-mastery-course/Level-2-Intermediate-PHP/projects/project-01-blog-cms

# 2. Install dependencies (generates autoloader)
composer install

# 3. Create database and run migrations
mysql -u root -p < migrations/init.sql

# 4. Configure database connection
# Edit config/database.php with your MySQL credentials

# 5. Start the PHP development server
composer serve

# 6. Visit http://localhost:8000
# Register a user, then manually set role to 'admin' in DB to access admin panel:
# UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

## Usage Examples

- Visit `/` to see published blog posts
- Register at `/register`, login at `/login`
- Create posts at `/posts/create`
- Category filter: `/?category=technology`
- Admin panel at `/admin` (requires admin role)
- Manage comments, users, categories from admin panel

## Database Schema

```
users        (id, username, email, password, role, created_at)
categories   (id, name, slug, description, created_at)
posts        (id, title, slug, content, excerpt, user_id, category_id, status, created_at, updated_at)
comments     (id, content, post_id, user_id, status, created_at)
```

## Code Structure

```
project-01-blog-cms/
├── composer.json
├── config/
│   └── database.php
├── migrations/
│   └── init.sql
├── public/
│   ├── index.php          # Front controller
│   └── style.css
├── src/
│   ├── Core/
│   │   ├── Database.php   # Singleton PDO wrapper
│   │   ├── Router.php     # Simple routing engine
│   │   ├── Session.php    # Session/flash message handler
│   │   ├── Request.php    # HTTP request abstraction
│   │   └── View.php       # Template renderer
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── PostController.php
│   │   ├── CommentController.php
│   │   └── AdminController.php
│   └── Models/
│       ├── User.php
│       ├── Post.php
│       ├── Comment.php
│       └── Category.php
└── templates/
    ├── layout.php
    ├── auth/
    │   ├── login.php
    │   └── register.php
    ├── posts/
    │   ├── index.php
    │   ├── show.php
    │   └── form.php
    ├── admin/
    │   ├── dashboard.php
    │   ├── posts.php
    │   ├── comments.php
    │   ├── users.php
    │   └── categories.php
    └── errors/
        └── 404.php
```

## Testing

Manual testing:
1. Register a new user and verify login works
2. Create a post with different statuses (draft/published)
3. Verify only published posts show on homepage
4. Add comments and test approval workflow
5. Test admin user management (role changes, deletion)
6. Test SQL injection by attempting special characters in forms

## Concepts Practiced

| Concept | Usage |
|---------|-------|
| OOP Classes | Models, Controllers, Core classes |
| PDO | Database.php with prepared statements |
| MVC | Controllers handle logic, Models handle data, Views render HTML |
| Namespaces | App\Core, App\Models, App\Controllers |
| PSR-4 Autoloading | composer.json autoload configuration |
| Authentication | Bcrypt hashing, session-based auth |
| Authorization | Role checks in AdminController |
| Exception Handling | Try-catch in Database.php constructor |
| CRUD | All models support CRUD operations |
