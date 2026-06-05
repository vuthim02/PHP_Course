# Chapter 11: Development Environment Setup

## Learning Objectives

By the end of this chapter you will:
- Set up a complete PHP development environment
- Install and configure PHP, a web server, and a database
- Use Composer for dependency management
- Configure Xdebug for debugging
- Set up Docker for PHP development

---

## 11.1 Local Development Options

| Method | Pros | Cons | Best For |
|--------|------|------|----------|
| Native install | Full control, no overhead | Version conflicts | Single project |
| Docker | Isolation, team consistency | Resource usage | Multiple projects |
| Laravel Herd/Valet | Simple, fast | macOS only | Laravel |
| XAMPP/MAMP | One-click install | Outdated, messy | Beginners |
| Devcontainer | VS Code integration | Complex setup | Teams |

---

## 11.2 Native Setup (Ubuntu/Debian)

```bash
#!/bin/bash
# Complete PHP Development Environment Setup

set -e

echo "=== PHP Development Environment Setup ===\n"

# System update
sudo apt update && sudo apt upgrade -y

# Essential tools
sudo apt install -y curl wget git unzip build-essential \
    software-properties-common

# Add PHP PPA (for latest versions)
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 with common extensions
sudo apt install -y php8.3-cli php8.3-fpm php8.3-common \
    php8.3-mysql php8.3-pgsql php8.3-sqlite3 \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-gd \
    php8.3-zip php8.3-bcmath php8.3-intl php8.3-redis \
    php8.3-imagick php8.3-xdebug

# Web server
sudo apt install -y nginx

# Database (choose one)
sudo apt install -y mysql-server      # MySQL
# sudo apt install -y postgresql      # PostgreSQL

# Cache
sudo apt install -y redis-server

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '$(curl -s https://composer.github.io/installer.sig 2>/dev/null)') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Verify
echo "\n=== Versions ==="
php -v
nginx -v
mysql --version 2>/dev/null || psql --version 2>/dev/null || echo "No DB found"
composer --version

echo "\n=== Setup Complete ==="
echo "PHP:       $(which php)"
echo "Composer:  $(which composer)"
echo "Nginx:     $(which nginx)"
echo "Web root:  /var/www/html"
```

---

## 11.3 Docker Setup

```dockerfile
# Dockerfile for PHP Development
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev libicu-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    mbstring intl xml curl zip bcmath opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP config
COPY php.ini /usr/local/etc/php/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    container_name: php-app
    volumes:
      - .:/var/www
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
    depends_on:
      - mysql
      - redis
    networks:
      - app-network

  nginx:
    image: nginx:alpine
    container_name: php-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - app-network

  mysql:
    image: mysql:8.0
    container_name: php-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: myapp
      MYSQL_USER: app
      MYSQL_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - app-network

  redis:
    image: redis:alpine
    container_name: php-redis
    ports:
      - "6379:6379"
    networks:
      - app-network

volumes:
  mysql_data:

networks:
  app-network:
    driver: bridge
```

```nginx
# docker/nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

---

## 11.4 PHP Configuration

```ini
; php.ini (development)
[PHP]
; Error reporting (show everything in dev)
error_reporting = E_ALL
display_errors = On
display_startup_errors = On
log_errors = On
error_log = /var/log/php_errors.log

; Resource limits
memory_limit = 256M
max_execution_time = 30
max_input_time = 60
max_input_vars = 2000

; File uploads
file_uploads = On
upload_max_filesize = 64M
post_max_size = 64M
max_file_uploads = 20

; Timezone
date.timezone = UTC

; Sessions
session.save_handler = files
session.save_path = /tmp
session.use_strict_mode = 1
session.use_only_cookies = 1
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Lax"

; OPcache
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.revalidate_freq = 2
opcache.enable_cli = 1
```

---

## 11.5 Xdebug Configuration

```ini
; xdebug.ini
zend_extension=xdebug

; Development settings
xdebug.mode = develop,debug,profile,trace
xdebug.start_with_request = trigger
xdebug.idekey = VSCODE

; IDE connection
xdebug.client_host = 127.0.0.1
xdebug.client_port = 9003
xdebug.discover_client_host = true

; Output
xdebug.output_dir = /tmp/xdebug
xdebug.profiler_output_name = cachegrind.out.%t.%p

; Performance (only when triggered)
xdebug.max_nesting_level = 512
```

---

## 11.6 VS Code Setup

```json
// .vscode/extensions.json
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "xdebug.php-debug",
        "neilbrayfield.php-docblocker",
        "recca0120.vscode-phpunit",
        "mehedidracula.php-namespace-resolver",
        "mikestead.dotenv",
        "EditorConfig.EditorConfig"
    ]
}
```

```json
// .vscode/settings.json
{
    "intelephense.files.maxSize": 5000000,
    "intelephense.environment.phpVersion": "8.3.0",
    "php.suggest.basic": false,
    "php.validate.enable": true,
    "php.validate.executablePath": "/usr/bin/php",
    "editor.formatOnSave": true,
    "files.associations": {
        "*.php": "php"
    }
}
```

---

## 11.7 Project Structure

```text
my-php-app/
├── public/
│   └── index.php              # Entry point (front controller)
├── src/
│   ├── Controllers/           # HTTP controllers
│   ├── Models/                # Business models
│   ├── Repositories/          # Data access
│   ├── Services/              # Business logic
│   ├── Middleware/             # Request middleware
│   ├── Exceptions/            # Custom exceptions
│   ├── Helpers/               # Utility functions
│   └── Kernel.php             # Application bootstrap
├── config/
│   ├── app.php                # Application config
│   ├── database.php           # Database config
│   └── routes.php             # Route definitions
├── templates/                 # View templates
├── migrations/                # Database migrations
├── tests/                     # Tests
│   ├── Unit/
│   └── Feature/
├── storage/                   # Cache, logs, uploads
├── vendor/                    # Composer dependencies
├── .env                       # Environment variables
├── .env.example               # Example env file
├── composer.json
├── README.md
└── docker-compose.yml
```

---

## 11.8 Exercises

1. Install PHP 8.x and verify with `php -v`
2. Create a `phpinfo()` page and examine the configuration
3. Set up a virtual host for a new project
4. Install a PHP package with Composer
5. Configure Xdebug and debug a simple script

---

## 11.9 Interview Questions

1. "How would you set up a PHP development environment from scratch?"
2. "What's the difference between Docker and a native setup?"
3. "How do you debug a PHP application?"
4. "What is OPcache and how does it work?"
5. "How would you configure PHP for development vs production?"

---

## Further Reading

- **Documentation:** [PHP Installation Manual](https://www.php.net/manual/en/install.php)
- **Documentation:** [Docker PHP Documentation](https://hub.docker.com/_/php/)
- **Resource:** [PHP The Right Way — Setup](https://phptherightway.com/#getting_started)
- **Tool:** [Laravel Valet](https://laravel.com/docs/valet) — macOS PHP dev

---

*End of Chapter 11. Proceed to Chapter 12: Tools of the Trade.*
