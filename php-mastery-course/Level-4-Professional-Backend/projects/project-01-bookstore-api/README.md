# Project 1: RESTful Book Store API

A professional-grade RESTful API for a bookstore built with PHP 8.x. Demonstrates industry-standard backend patterns including JWT authentication, rate limiting, API versioning, HATEOAS pagination, and OpenAPI documentation.

## Professional Backend Concepts Demonstrated

- **REST API Design**: Resource-oriented architecture with proper HTTP methods, status codes, and HATEOAS links
- **JWT Authentication**: Access + refresh token flow with configurable expiry
- **API Versioning**: URL prefix versioning (`/v1/`)
- **Rate Limiting**: Sliding window algorithm, 100 requests/minute per IP
- **Pagination & Filtering**: Offset-based pagination with sort, search, and filter support
- **Input Validation**: Comprehensive request validation with meaningful error messages
- **PSR-4 Autoloading**: Clean namespace structure following PHP-FIG standards
- **CORS Middleware**: Configurable cross-origin support with preflight handling
- **OpenAPI 3.0**: Auto-generated API documentation via swagger-php annotations
- **Security**: Password hashing (bcrypt, cost 12), SQL injection prevention (prepared statements), security headers

## API Documentation

### Base URL: `http://localhost:8080/v1`

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/v1/auth/register` | Register a new user |
| POST | `/v1/auth/login` | Login and get tokens |
| POST | `/v1/auth/refresh` | Refresh access token |
| GET | `/v1/auth/me` | Get current user (auth) |

### Books

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/v1/books` | List books (paginated, filterable) |
| GET | `/v1/books/{id}` | Get a single book |
| POST | `/v1/books` | Create a book (auth) |
| PUT | `/v1/books/{id}` | Update a book (auth) |
| DELETE | `/v1/books/{id}` | Delete a book (auth) |

### Authors

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/v1/authors` | List authors |
| GET | `/v1/authors/{id}` | Get an author |
| POST | `/v1/authors` | Create an author (auth) |
| PUT | `/v1/authors/{id}` | Update an author (auth) |
| DELETE | `/v1/authors/{id}` | Delete an author (auth) |

### Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/v1/categories` | List categories |
| GET | `/v1/categories/{id}` | Get a category |
| POST | `/v1/categories` | Create a category (auth) |
| PUT | `/v1/categories/{id}` | Update a category (auth) |
| DELETE | `/v1/categories/{id}` | Delete a category (auth) |

### Reviews

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/v1/books/{id}/reviews` | List reviews for a book |
| POST | `/v1/books/{id}/reviews` | Add a review (auth) |
| DELETE | `/v1/reviews/{id}` | Delete a review (auth) |

### Request/Response Examples

**Register user:**
```bash
curl -X POST http://localhost:8080/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"SecurePass1"}'
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com", "role": "user" },
    "tokens": {
      "access_token": "eyJ...",
      "refresh_token": "eyJ...",
      "expires_in": 3600,
      "token_type": "Bearer"
    }
  }
}
```

**List books with filtering:**
```bash
curl "http://localhost:8080/v1/books?search=harry&sort=price&order=asc&page=1&per_page=10"
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "meta": { "current_page": 1, "per_page": 10, "total": 5, "last_page": 1 },
  "links": {
    "self": "/v1/books?page=1&per_page=10",
    "first": "/v1/books?page=1&per_page=10",
    "last": "/v1/books?page=1&per_page=10"
  }
}
```

## Setup Instructions

### Prerequisites
- PHP 8.1+
- MySQL 8.0+
- Composer

### Installation
```bash
# 1. Navigate to project directory
cd project-01-bookstore-api

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials and JWT secret

# 4. Create database and run migrations
mysql -u root -p < migrations/init.sql

# 5. Generate OpenAPI docs
composer docs

# 6. Start development server
composer serve
# Server runs at http://localhost:8080
```

### Testing
```bash
composer test
```

Health check:
```bash
curl http://localhost:8080/health
```

API docs:
```bash
curl http://localhost:8080/docs
```

## Deployment Considerations

- **Database**: Use connection pooling (e.g., pgbouncer for Postgres) and read replicas for scaling
- **JWT**: Store secrets in environment variables or a secrets manager (HashiCorp Vault, AWS Secrets Manager). Never commit `.env` to version control.
- **Rate Limiting**: For distributed deployments, replace the database-backed limiter with Redis (sliding window via SORTED SET)
- **Caching**: Add Redis caching layer for frequently accessed resources (books list, categories)
- **HTTPS**: Always terminate TLS at the load balancer; set `Strict-Transport-Security` header
- **PHP-FPM**: Use PHP-FPM with OPcache in production; tune `pm.max_children` based on available memory
- **Monitoring**: Integrate with Sentry for error tracking and Prometheus for metrics
- **API Gateway**: Consider using Kong or AWS API Gateway for additional rate limiting, caching, and DDOS protection
