# Project 3: URL Shortener

A bit.ly-style URL shortening service. Paste a long URL, get a short 6-character code. Track clicks with referrer, IP, and timestamp. Links can have custom aliases and expiration dates.

## Learning Objectives

- **OOP**: Link and Click models with relationships
- **PDO**: Prepared statements for tracking clicks
- **Base62 Encoding**: Short code generation algorithm
- **HTTP Redirects**: 301 permanent redirect implementation
- **Click Tracking**: Record IP, referer, user-agent per visit
- **Link Expiration**: Date-based expiry logic
- **Authentication**: User-scoped link management
- **Namespaces & PSR-4**: Composer autoloading

## Features

- Shorten long URLs with random 6-char base62 codes
- Custom aliases for personalized short URLs
- Optional link expiration dates
- 301 redirect to original URL
- Click tracking (IP, referrer, user agent, timestamp)
- Per-link statistics page with recent clicks and top referrers
- User accounts to manage all links
- Delete links

## Setup Instructions

```bash
cd php-mastery-course/Level-2-Intermediate-PHP/projects/project-03-url-shortener

composer install

mysql -u root -p < migrations/init.sql

# Edit config/database.php with your credentials

composer serve
# Visit http://localhost:8002
```

## Usage Examples

1. Visit the homepage and paste a URL to shorten (no login needed)
2. Register to manage your links
3. Create custom short codes like `my-link`
4. Share the short URL: `http://localhost:8002/abc123`
5. Visit `/links/1/stats` to see click analytics
6. Set expiration dates for temporary links

## Database Schema

```
users    (id, username, email, password, created_at)
links    (id, long_url, short_code, user_id, expires_at, clicks, created_at)
clicks   (id, link_id, ip_address, referer, user_agent, clicked_at)
```

## Code Structure

```
project-03-url-shortener/
├── composer.json
├── config/database.php
├── migrations/init.sql
├── public/index.php
├── src/
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Request.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── View.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── LinkController.php
│   │   ├── RedirectController.php
│   │   └── StatsController.php
│   └── Models/
│       ├── Click.php
│       ├── Link.php
│       └── User.php
└── templates/
    ├── layout.php
    ├── auth/login.php
    ├── auth/register.php
    ├── links/index.php
    ├── links/list.php
    ├── links/create.php
    └── links/stats.php
```

## Concepts Practiced

| Concept | Usage |
|---------|-------|
| OOP | Data models, controllers, core classes |
| PDO | Prepared statements, click insertion |
| HTTP | 301 redirects, URL validation |
| Cryptography | Random token generation (random_int) |
| Base62 | Alphanumeric short code generation |
| Auth | Session-based user authentication |
| Analytics | Click tracking with IP and referer |
| CRUD | Link creation, deletion, stats viewing |
