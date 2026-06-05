# Project 4: Shopping Cart System

A complete e-commerce shopping cart system with product catalog, session-based cart management, checkout flow, and order history for logged-in users.

## Learning Objectives

- **OOP**: Product, Cart, Order, OrderItem, User models
- **Session Cart**: Storing cart items in `$_SESSION` between requests
- **Service Layer**: `CartService` for checkout logic and transactions
- **Database Transactions**: Atomic checkout with stock updates
- **PDO**: CRUD operations with prepared statements
- **Dependency Injection**: Service class composition
- **MVC**: Full separation for e-commerce flow
- **Namespaces & PSR-4**: Composer autoloading

## Features

- Product catalog with pagination
- Product detail pages with add-to-cart
- Session-based shopping cart
- Update quantities and remove items
- Cart total calculation
- Checkout with stock validation
- Order confirmation and history
- Database transactions for atomic purchases
- Sample product data included

## Setup Instructions

```bash
cd php-mastery-course/Level-2-Intermediate-PHP/projects/project-04-shopping-cart

composer install

mysql -u root -p < migrations/init.sql

# Edit config/database.php

composer serve
# Visit http://localhost:8003
```

## Usage Examples

1. Browse products at `/products`
2. View product details and add to cart
3. Click cart icon to review items
4. Update quantities or remove items
5. Register/login to checkout
6. Complete checkout — cart items are converted to an order
7. View order history at `/orders`
8. Stock is automatically decremented on checkout

## Database Schema

```
users        (id, username, email, password, created_at)
products     (id, name, slug, description, price, stock, image_url, created_at)
orders       (id, user_id, total, status, created_at)
order_items  (id, order_id, product_id, product_name, price, quantity, subtotal)
```

## Code Structure

```
project-04-shopping-cart/
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
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   └── ProductController.php
│   ├── Models/
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Product.php
│   │   └── User.php
│   └── Services/
│       └── CartService.php
└── templates/
    ├── layout.php
    ├── auth/login.php
    ├── auth/register.php
    ├── cart/show.php
    ├── errors/404.php
    ├── orders/index.php
    ├── orders/show.php
    ├── products/index.php
    └── products/show.php
```

## Concepts Practiced

| Concept | Usage |
|---------|-------|
| OOP | Models with typed properties and methods |
| PDO | Prepared statements, transactions |
| Session Cart | $_SESSION-based cart storage |
| Service Layer | CartService for checkout business logic |
| Transactions | Atomic stock deduction + order creation (rollback on failure) |
| MVC | Controllers → Services → Models → Views |
| Namespaces | PSR-4 autoloading |
| CRUD | Product browsing, cart CRUD, order history |
