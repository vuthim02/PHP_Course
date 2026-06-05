# Chapter 20: Capstone Project — Complete PHP Application

## Build: User Management System

Build a complete user management system using everything from Level 1.

### Requirements

```php
<?php
/**
 * User Management System
 * 
 * Features:
 * ✅ User registration with validation
 * ✅ User login/logout with sessions
 * ✅ CRUD operations (Create, Read, Update, Delete users)
 * ✅ Form validation and CSRF protection
 * ✅ File upload for avatars
 * ✅ Session-based authentication
 * ✅ JSON API endpoints
 * ✅ Error handling and logging
 */
```

### Project Structure

```
user-management/
├── public/
│   └── index.php              # Entry point, router
├── src/
│   ├── Config/
│   │   └── database.php       # Database configuration
│   ├── Controllers/
│   │   └── UserController.php # User CRUD logic
│   ├── Models/
│   │   └── User.php           # User data model
│   ├── Helpers/
│   │   ├── Auth.php           # Authentication
│   │   ├── Session.php        # Session management
│   │   ├── Validator.php      # Input validation
│   │   └── functions.php      # Helper functions
│   ├── Middleware/
│   │   └── CsrfMiddleware.php # CSRF protection
│   └── Exceptions/
│       ├── ValidationException.php
│       └── AuthException.php
├── templates/
│   ├── layout.php             # Main layout
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   └── users/
│       ├── index.php          # List users
│       ├── show.php           # View user
│       ├── create.php         # Create form
│       └── edit.php           # Edit form
├── storage/
│   ├── uploads/               # Avatar uploads
│   └── logs/                  # Error logs
├── .env                       # Environment config
└── data/
    └── users.json             # File-based storage
```

### Implementation

Full source code available in the projects directory. This project exercises every concept from Level 1:
- Variables and data types
- Arrays and strings
- Control flow and loops
- Functions and includes
- Forms and user input
- Sessions and cookies
- File operations
- Error handling
- Security (XSS, CSRF)
- File organization
