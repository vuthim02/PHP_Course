# Project 5: User Authentication & Authorization System

A complete authentication and authorization system with user registration, login, password reset, "remember me" functionality, profile management, role-based access control (RBAC), and permission management.

## Learning Objectives

- **OOP**: User, Role, Permission models with many-to-many relationships
- **Authentication**: Registration, login, logout, bcrypt hashing, remember-me tokens
- **Password Reset**: Token-based reset flow with expiration
- **Authorization**: Role-based access control (RBAC) with granular permissions
- **Middleware**: Route protection via AuthMiddleware and RoleMiddleware
- **Service Layer**: AuthService, PasswordService with error handling
- **PDO**: Complex queries with joins for permission checking
- **Namespaces & PSR-4**: Full Composer autoloading structure
- **Security**: Session regeneration, secure cookies, token verification
- **MVC**: Routes → Middleware → Controllers → Services → Models → Views

## Features

- User registration with validation
- Login with "Remember Me" (30-day cookie token)
- Password reset via email token
- Profile management (edit profile, change password)
- Role-based access control (admin, editor, user)
- Granular permission system (create, edit, delete, publish)
- User management (list, assign roles, suspend, delete)
- Role CRUD with permission assignment
- Permission CRUD
- Middleware-protected routes
- Admin dashboard with statistics

## Setup Instructions

```bash
cd php-mastery-course/Level-2-Intermediate-PHP/projects/project-05-auth-system

composer install

mysql -u root -p < migrations/init.sql

# Edit config/database.php with your credentials

composer serve
# Visit http://localhost:8004
```

## Usage Examples

1. Register at `/register`
2. Login at `/login` (check "Remember Me" for persistent login)
3. View/edit profile at `/profile`
4. Change password from profile page
5. Use "Forgot Password" flow at `/forgot-password`
6. Admin panel at `/admin` (change a user's role to 'admin' in DB)
7. Manage users, roles, and permissions from admin panel
8. Assign permissions to roles, roles to users

## Database Schema

```
users              (id, username, email, password, role, status, remember_token, reset_token, reset_token_expires, created_at)
roles              (id, name, description)
permissions        (id, name, description)
role_permissions   (role_id, permission_id) [many-to-many]
user_roles         (user_id, role_id)       [many-to-many]
```

## Code Structure

```
project-05-auth-system/
├── composer.json
├── config/database.php
├── migrations/init.sql
├── public/index.php          # Front controller with remember-me logic
├── src/
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Request.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── View.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ProfileController.php
│   │   └── AdminController.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   └── RoleMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Services/
│       ├── AuthService.php
│       └── PasswordService.php
└── templates/
    ├── layout.php
    ├── auth/login.php
    ├── auth/register.php
    ├── auth/forgot.php
    ├── auth/reset.php
    ├── profile/show.php
    ├── admin/dashboard.php
    ├── admin/users.php
    ├── admin/roles.php
    ├── admin/permissions.php
    └── errors/404.php
```

## Concepts Practiced

| Concept | Usage |
|---------|-------|
| OOP | Models, Controllers, Services, Middleware |
| PDO | Prepared statements, joins for permission checks |
| MVC | Full architecture with service layer |
| Namespaces | PSR-4 autoloading (App\Core, App\Models, etc.) |
| Authentication | Bcrypt, session, remember-me tokens |
| Authorization | Role-based + permission-based access |
| Password Reset | Token generation, hashing, expiry |
| Middleware | Route protection pattern |
| Service Layer | AuthService, PasswordService |
| Security | Session regeneration, secure tokens |
| CRUD | Full RBAC management |
