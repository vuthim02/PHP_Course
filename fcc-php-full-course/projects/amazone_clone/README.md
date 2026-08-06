# amazone — a plain-PHP Amazon-style storefront

A learning e-commerce webapp that looks and works like amazon.com, built from
scratch with **plain PHP 8.5 + SQLite + vanilla HTML/CSS/JS**. Checkout uses
**Stripe in test mode** — real-looking cards, zero real money. Full plan:
see [plan.md](plan.md).

## 1. Setup (no server, no password)

SQLite stores the whole database in one file. Everything is included:

```bash
# a) Create the database (creates database/amazone.sqlite)
php database/setup.php

# b) Check config (SQLite needs no credentials — just a file path)
cp app/config.example.php app/config.php   # optional
```

## 2. Stripe (test mode, ~5 min, free)

1. Create a free account: https://dashboard.stripe.com/register
2. Open **Developers → API keys** (make sure you're in **Test mode**):
   https://dashboard.stripe.com/test/apikeys
3. Copy `pk_test_...` and `sk_test_...` into the `"stripe"` section of
   `app/config.php`.
4. Test card: `4242 4242 4242 4242` — any future expiry, any CVC, any ZIP.

## 3. Run

```bash
php -S localhost:4000 -t public public/router.php
```

Open http://localhost:4000

To reset to sample data anytime: `php database/setup.php` and refresh.

## 4. Pages

| URL | Page |
|---|---|
| `/` | Home: hero + categories + featured products |
| `/products` | All products (filter by `?category=2`) |
| `/product/{slug}` | Product detail + Add to Cart |
| `/search?q=...` | Product search |
| `/cart` | Cart: qty, delete, subtotal (FREE delivery over $25) |
| `/checkout` | Shipping address + Stripe card payment |
| `/order/{id}` | Order confirmation page |

## 5. Structure

```
public/   only folder exposed to the web server (index.php = front controller)
app/
  controllers/  Home, Product, Cart, Checkout (call models, render views)
  models/       Product, Cart, Order — all SQL here (PDO prepared statements)
  Stripe/       Client.php — cURL wrapper for the Stripe REST API (no SDK)
  views/        HTML templates (layout/, products/, cart/checkout/order)
  config.php    settings — git-ignored, copy from config.example.php
  db.php        one shared PDO connection (SQLite by default, MySQL optional)
  functions.php helpers: e(), money(), stars(), csrf_*(), config()
database/     schema.sql + seed.sql + setup.php + amazone.sqlite (generated)
```

## 6. Roadmap status

- [x] Phase 0 setup: front controller, router, SQLite schema, seed data
- [x] Phase 1: home, product list, product detail, search
- [x] Phase 3: working cart (session) + cart page
- [x] Phase 4: checkout + Stripe test-mode payment + order confirmation
- [ ] Phase 2: accounts (register/login) — needed for order history
- [ ] Phase 5+: reviews, admin panel — see plan.md

