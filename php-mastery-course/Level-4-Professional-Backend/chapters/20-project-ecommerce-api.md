# Chapter 20: Project: E-Commerce API

## Project Overview

Build a complete e-commerce backend API with products, categories, cart, checkout, payments, and order management.

---

## 20.1 Requirements

### Features
- Product catalog with categories
- Shopping cart management
- Checkout with Stripe payment
- User accounts with address management
- Order history and tracking
- Admin product management
- Search with filters
- Rate limiting
- API documentation (OpenAPI)
- Webhook handling

### Technical Stack
- Pure PHP with PSR-7/15 middleware
- MySQL with Elasticsearch
- Redis for caching and sessions
- RabbitMQ for async jobs
- Stripe for payments

---

## 20.2 Database Schema

```sql
-- Products
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2) NULL,
    stock_quantity INT UNSIGNED DEFAULT 0,
    category_id BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    session_id VARCHAR(255),
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Orders
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED,
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled')
        DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    payment_intent_id VARCHAR(255),
    shipping_address_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

---

## 20.3 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/products | List products (paginated) |
| GET | /api/products/{id} | Get product details |
| GET | /api/categories | List categories |
| GET | /api/cart | Get current cart |
| POST | /api/cart/items | Add item to cart |
| PUT | /api/cart/items/{id} | Update cart item |
| DELETE | /api/cart/items/{id} | Remove cart item |
| POST | /api/checkout | Process checkout |
| GET | /api/orders | List user orders |
| GET | /api/orders/{id} | Get order details |
| POST | /api/webhooks/stripe | Stripe webhook handler |

---

## 20.4 Deliverables

1. Complete RESTful API for e-commerce
2. Product catalog with search and filtering
3. Shopping cart with persistent storage
4. Checkout with Stripe payment integration
5. Order management system
6. Admin endpoints for product management
7. OpenAPI documentation
8. Rate limiting and authentication

---

## Further Reading

- **Doc:** [Stripe API](https://stripe.com/docs/api)
- **Doc:** [REST API Best Practices](https://restfulapi.net/)
