CREATE DATABASE IF NOT EXISTS shopping_cart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopping_cart;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);

-- Sample products
INSERT INTO products (name, slug, description, price, stock) VALUES
('Wireless Mouse', 'wireless-mouse', 'Ergonomic wireless mouse with USB receiver. 2.4GHz connection, 1200 DPI optical sensor.', 29.99, 50),
('Mechanical Keyboard', 'mechanical-keyboard', 'RGB mechanical keyboard with Cherry MX Blue switches. Full 104-key layout with aluminum frame.', 89.99, 30),
('USB-C Hub', 'usb-c-hub', '7-in-1 USB-C hub with HDMI, USB-A 3.0, SD card reader, and 100W PD charging.', 45.99, 40),
('HDMI Cable 6ft', 'hdmi-cable-6ft', 'High-speed HDMI 2.1 cable. Supports 4K@60Hz, HDR, and Ethernet. Gold-plated connectors.', 12.99, 100),
('Webcam 1080p', 'webcam-1080p', 'Full HD 1080p webcam with built-in microphone. Plug-and-play USB, adjustable clip.', 59.99, 25),
('Laptop Stand', 'laptop-stand', 'Adjustable aluminum laptop stand. Ergonomic design, foldable, compatible with 10-17 inch laptops.', 34.99, 35);
