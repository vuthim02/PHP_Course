-- seed.sql — sample categories + products for development.
-- Executed by database/setup.php.

DELETE FROM return_items;
DELETE FROM returns;
DELETE FROM order_items;
DELETE FROM orders;
DELETE FROM cart_items;
DELETE FROM reviews;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM users;

INSERT INTO categories (id, name, slug, image_url) VALUES
    (1, 'Electronics',       'electronics',       '/img/categories/cat-electronics.jpg'),
    (2, 'Home & Kitchen',    'home-kitchen',      '/img/categories/cat-home.jpg'),
    (3, 'Books',             'books',             '/img/categories/cat-books.jpg'),
    (4, 'Sports & Outdoors', 'sports-outdoors',   '/img/categories/cat-sports.jpg'),
    (5, 'Gift Cards',        'gift-cards',        '/img/categories/cat-giftcards.svg');

-- Demo admin account (password: admin123) — for the Phase 6 admin panel.
INSERT INTO users (name, email, password_hash, is_admin) VALUES
    ('Admin', 'admin@amazone.local',
     '$2y$12$cUs4ax3X3lZjrH59qLNizueSLZszL6.n2n6Pv5FGfEJ/xdJLpzN9q', 1);

INSERT INTO products (category_id, name, slug, description, price, deal_price, stock, image_url, rating_avg, rating_count) VALUES
    (1, 'Wireless Bluetooth Headphones', 'wireless-bluetooth-headphones',
     'Over-ear headphones with 30-hour battery life, active noise canceling and a built-in microphone.',
     89.99, 79.99, 120, '/img/products/headphones.jpg', 4.5, 210),
    (1, '4K Action Camera', '4k-action-camera',
     'Waterproof 4K action camera with image stabilization and a touch screen.',
     199.99, NULL, 60, '/img/products/camera.jpg', 4.2, 95),
    (1, 'USB-C Fast Charger 65W', 'usb-c-fast-charger-65w',
     'Compact 65W wall charger with two USB-C ports and one USB-A port.',
     39.99, 29.99, 300, '/img/products/charger.jpg', 4.7, 340),
    (1, 'RGB Mechanical Keyboard', 'rgb-mechanical-keyboard',
     'RGB backlit mechanical keyboard with hot-swappable brown switches.',
     74.50, 59.99, 80, '/img/products/keyboard.jpg', 4.4, 150),
    (2, 'Non-Stick Cookware Set', 'non-stick-cookware-set',
     '10-piece ceramic non-stick cookware set. Dishwasher safe.',
     129.00, 99.00, 45, '/img/products/cookware.jpg', 4.6, 180),
    (2, 'Insulated Travel Mug', 'insulated-travel-mug',
     'Stainless steel travel mug that keeps drinks hot for 12 hours.',
     24.99, NULL, 200, '/img/products/mug.jpg', 4.8, 420),
    (2, 'Espresso Machine', 'espresso-machine',
     '15-bar espresso machine with milk frother and a 1.5L water tank.',
     249.00, 199.00, 25, '/img/products/espresso.jpg', 4.3, 88),
    (3, 'Learn PHP the Modern Way', 'learn-php-the-modern-way',
     'A hands-on guide to building real applications with PHP 8.',
     34.99, NULL, 500, '/img/products/phpbook.jpg', 4.9, 56),
    (3, 'The Alchemist', 'the-alchemist',
     'Paulo Coelho''s classic novel about following your dreams.',
     12.50, NULL, 400, '/img/products/novel.jpg', 4.7, 610),
    (3, 'Clean Code', 'clean-code',
     'A handbook of agile software craftsmanship by Robert C. Martin.',
     32.00, 22.40, 250, '/img/products/cleancode.jpg', 4.8, 970),
    (4, 'Non-Slip Yoga Mat', 'non-slip-yoga-mat',
     'Extra thick 8mm non-slip yoga mat with a carry strap.',
     19.99, 15.99, 350, '/img/products/yoga.jpg', 4.5, 520),
    (4, 'Camping Tent 4-Person', 'camping-tent-4-person',
     'Waterproof dome tent that sets up in under 10 minutes.',
     109.99, 89.99, 40, '/img/products/tent.jpg', 4.1, 132),
    (5, 'amazone Gift Card $25', 'gift-card-25',
     'A $25 amazone gift card. Redeemable on millions of items.',
     25.00, NULL, 1000, '/img/giftcards/giftcard-25.svg', 4.8, 2100),
    (5, 'amazone Gift Card $50', 'gift-card-50',
     'A $50 amazone gift card. Perfect for birthdays and holidays.',
     50.00, NULL, 1000, '/img/giftcards/giftcard-50.svg', 4.8, 3400),
    (5, 'amazone Gift Card $100', 'gift-card-100',
     'A $100 amazone gift card. Good for almost everything.',
     100.00, NULL, 1000, '/img/giftcards/giftcard-100.svg', 4.9, 5200),
    (5, 'amazone Gift Card $200', 'gift-card-200',
     'A $200 amazone gift card. The gift of choice.',
     200.00, NULL, 1000, '/img/giftcards/giftcard-200.svg', 4.9, 1200);
