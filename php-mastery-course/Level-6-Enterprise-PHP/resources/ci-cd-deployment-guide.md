# CI/CD & Deployment — Enterprise Reference

## GitHub Actions — PHP CI Pipeline

```yaml
# .github/workflows/ci.yml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  quality:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['8.1', '8.2', '8.3']

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: test
        options: >-
          --health-cmd "mysqladmin ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 3306:3306
      redis:
        image: redis:7
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: mbstring, pdo_mysql, redis, pcov
          coverage: pcov
          tools: composer:v2

      - name: Cache Composer dependencies
        uses: actions/cache@v3
        with:
          path: vendor
          key: composer-${{ hashFiles('composer.lock') }}

      - name: Install dependencies
        run: composer install --no-progress --prefer-dist

      - name: Run PHPStan
        run: vendor/bin/phpstan analyse --level=max src/ tests/

      - name: Run PHP-CS-Fixer
        run: vendor/bin/php-cs-fixer fix --dry-run --diff src/

      - name: Run PHPUnit
        run: vendor/bin/phpunit --coverage-clover coverage.xml
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: test
          DB_USERNAME: root
          DB_PASSWORD: root

      - name: Upload coverage
        uses: codecov/codecov-action@v3

  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Composer audit
        run: composer audit

  deploy:
    needs: [quality, security]
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to production
        uses: deploy/action@v1
        with:
          script: |
            cd /var/www/app
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            sudo systemctl reload php8.2-fpm
```

## Docker — Production Dockerfile

```dockerfile
# Dockerfile — multi-stage build
FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

FROM php:8.2-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    opcache \
    zip \
    bcmath

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# OPcache config
COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/app

# Copy application (excluding dev files)
COPY --from=composer /app/vendor ./vendor
COPY . .

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - ./storage:/var/www/app/storage
    networks:
      - app-network
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_started

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - .:/var/www/app
      - .docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - app-network
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5
    networks:
      - app-network

  redis:
    image: redis:7-alpine
    volumes:
      - redis-data:/data
    networks:
      - app-network

  queue:
    build:
      context: .
      dockerfile: Dockerfile
    command: php artisan queue:work --sleep=3 --tries=3
    volumes:
      - ./storage:/var/www/app/storage
    networks:
      - app-network
    depends_on:
      - mysql
      - redis

  cron:
    build:
      context: .
      dockerfile: Dockerfile
    command: crond -f -l 2
    volumes:
      - .docker/crontab:/etc/crontabs/www-data
    networks:
      - app-network

volumes:
  mysql-data:
  redis-data:

networks:
  app-network:
    driver: bridge
```

## Kubernetes Deployment

```yaml
# k8s/deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: php-app
  template:
    metadata:
      labels:
        app: php-app
    spec:
      containers:
        - name: php-app
          image: registry.example.com/php-app:latest
          ports:
            - containerPort: 9000
          env:
            - name: APP_ENV
              value: "production"
            - name: DB_HOST
              valueFrom:
                secretKeyRef:
                  name: app-secrets
                  key: db-host
            - name: DB_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: app-secrets
                  key: db-password
            - name: REDIS_HOST
              value: "redis-service"
          resources:
            requests:
              memory: "128Mi"
              cpu: "250m"
            limits:
              memory: "512Mi"
              cpu: "500m"
          livenessProbe:
            tcpSocket:
              port: 9000
            initialDelaySeconds: 10
            periodSeconds: 10
          readinessProbe:
            exec:
              command:
                - php
                - artisan
                - health:check
            initialDelaySeconds: 5
            periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: php-app-service
spec:
  selector:
    app: php-app
  ports:
    - port: 9000
      targetPort: 9000
---
apiVersion: v1
kind: HorizontalPodAutoscaler
metadata:
  name: php-app-hpa
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: php-app
  minReplicas: 3
  maxReplicas: 20
  metrics:
    - type: Resource
      resource:
        name: cpu
        target:
          type: Utilization
          averageUtilization: 70
    - type: Resource
      resource:
        name: memory
        target:
          type: Utilization
          averageUtilization: 80
```

## Serverless (Bref + AWS Lambda)

```yaml
# serverless.yml (Bref)
service: php-app

provider:
  name: aws
  region: us-east-1

plugins:
  - ./vendor/bref/bref

functions:
  api:
    handler: public/index.php
    runtime: php-83
    events:
      - httpApi: '*'
    layers:
      - ${bref:layer.php-83}
    environment:
      APP_ENV: production
      DB_HOST: ${env:DB_HOST}
      DB_DATABASE: ${env:DB_DATABASE}

  worker:
    handler: worker.php
    runtime: php-83
    timeout: 300
    layers:
      - ${bref:layer.php-83}
    events:
      - sqs:
          arn: arn:aws:sqs:us-east-1:123456789012:queue-name

# RDS or Aurora for DB
# ElastiCache for Redis
```

## Deployment Script

```bash
#!/bin/bash
# deploy.sh — zero-downtime deployment

set -euo pipefail

APP_DIR="/var/www/app"
RELEASE_DIR="/var/www/releases/$(date +%Y%m%d%H%M%S)"

echo "→ Creating release directory"
mkdir -p "$RELEASE_DIR"

echo "→ Cloning repository"
git clone --depth 1 git@github.com:org/app.git "$RELEASE_DIR"

echo "→ Installing dependencies"
cd "$RELEASE_DIR"
composer install --no-dev --no-interaction --optimize-autoloader

echo "→ Setting up environment"
ln -sf "$APP_DIR/.env" "$RELEASE_DIR/.env"

echo "→ Running database migrations"
php artisan migrate --force

echo "→ Caching config"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ Symlinking storage"
ln -sf "$APP_DIR/storage" "$RELEASE_DIR/storage"

echo "→ Switching symlink"
ln -sfn "$RELEASE_DIR" "$APP_DIR/current"

echo "→ Restarting PHP-FPM"
sudo systemctl reload php8.2-fpm

echo "→ Deployment complete"
```

## Monitoring & Observability

```php
// Application health check endpoint
class HealthCheckController
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
        ];

        $healthy = !in_array(false, array_column($checks, 'healthy'), true);

        return response()->json([
            'status' => $healthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo()->query('SELECT 1');
            return ['healthy' => true];
        } catch (\Exception $e) {
            return ['healthy' => false, 'error' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            Redis::ping();
            return ['healthy' => true];
        } catch (\Exception $e) {
            return ['healthy' => false, 'error' => $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        $path = storage_path('logs');
        return [
            'healthy' => is_writable($path),
            'disk_free' => disk_free_space($path),
        ];
    }
}
```

## Rollback Strategy

```bash
#!/bin/bash
# rollback.sh

RELEASES_DIR="/var/www/releases"
CURRENT_LINK="/var/www/app/current"

# Get second-last release
PREVIOUS=$(ls -1 "$RELEASES_DIR" | sort | tail -2 | head -1)

if [ -z "$PREVIOUS" ]; then
    echo "No previous release to rollback to"
    exit 1
fi

echo "→ Rolling back to $PREVIOUS"
ln -sfn "$RELEASES_DIR/$PREVIOUS" "$CURRENT_LINK"

echo "→ Running rollback migration (if applicable)"
cd "$CURRENT_LINK"
php artisan migrate:rollback --step=1

echo "→ Reloading PHP-FPM"
sudo systemctl reload php8.2-fpm

echo "→ Rollback complete"
```
