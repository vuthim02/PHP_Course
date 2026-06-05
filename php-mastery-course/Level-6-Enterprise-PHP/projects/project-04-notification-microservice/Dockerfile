FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    librabbitmq-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Install AMQP extension
RUN pecl install amqp && docker-php-ext-enable amqp

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Create storage directories
RUN mkdir -p storage/logs storage/templates

# No EXPOSE — this is a CLI worker, not an HTTP server
# Default: run the consumer worker
CMD ["php", "bin/consumer.php"]
