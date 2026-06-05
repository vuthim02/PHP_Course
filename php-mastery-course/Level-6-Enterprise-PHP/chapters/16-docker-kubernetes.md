# Chapter 16: Docker and Kubernetes

## Learning Objectives

- Containerize PHP applications for development and production
- Create multi-container setups with Docker Compose
- Write production-grade Dockerfiles for PHP
- Deploy and manage PHP apps on Kubernetes
- Configure Helm charts for repeatable deployments
- Implement CI/CD with Docker registries
- Monitor containerized PHP applications

---

## 16.1 Docker Fundamentals

### What is Containerization?

A container packages your application with all its dependencies (PHP runtime, extensions, system libraries, config files) into a single, portable unit that runs identically everywhere.

```
┌─────────────────────────────────┐
│  Container                      │
│  ┌───────────────────────────┐  │
│  │  PHP Application          │  │
│  ├───────────────────────────┤  │
│  │  PHP 8.2 + Extensions     │  │
│  ├───────────────────────────┤  │
│  │  System Libraries         │  │
│  ├───────────────────────────┤  │
│  │  Alpine Linux (minimal)   │  │
│  └───────────────────────────┘  │
└─────────────────────────────────┘
```

### Dockerfile Best Practices for PHP

```dockerfile
# ── Stage 1: Dependencies ──────────────────────────────────
FROM php:8.2-fpm-alpine AS vendor

# Install system dependencies
RUN apk add --no-cache \
    zip unzip git \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        zip \
        bcmath \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy only dependency files first (leverages Docker cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ── Stage 2: Build assets ──────────────────────────────────
FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --production
COPY resources/ ./resources/
RUN npm run production

# ── Stage 3: Production image ──────────────────────────────
FROM php:8.2-fpm-alpine

# Install only runtime system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath \
    opcache

# Configure OPcache for production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Configure PHP-FPM for production
RUN { \
    echo '[global]'; \
    echo 'pid = /run/php-fpm.pid'; \
    echo 'error_log = /proc/self/fd/2'; \
    echo 'log_level = warning'; \
    echo ''; \
    echo '[www]'; \
    echo 'pm = dynamic'; \
    echo 'pm.max_children = 50'; \
    echo 'pm.start_servers = 5'; \
    echo 'pm.min_spare_servers = 5'; \
    echo 'pm.max_spare_servers = 15'; \
    echo 'pm.max_requests = 500'; \
    echo 'catch_workers_output = yes'; \
    echo 'access.log = /proc/self/fd/2'; \
} > /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /app

# Copy from build stages
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=assets /app/public/build/ ./public/build/
COPY . .

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

# Configure supervisord to run Nginx + PHP-FPM
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### Multi-stage Build Benefits

| Stage | Purpose | Image Size |
|-------|---------|------------|
| `vendor` | Composer deps (PHP 8.2 + tools) | ~300MB |
| `assets` | NPM build (Node 20) | ~200MB |
| `production` | Runtime only (no build tools) | ~80MB |

Final image is **~80MB** instead of ~500MB without multi-stage.

---

## 16.2 Docker Compose for Development

### Full Development Stack

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: .
      target: vendor
    volumes:
      - .:/app
      - ./docker/php.ini:/usr/local/etc/php/conf.d/app.ini
    environment:
      APP_ENV: local
      DB_HOST: mysql
      REDIS_HOST: redis
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
      - .:/app
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
      - ./docker/ssl:/etc/nginx/ssl
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root}
      MYSQL_DATABASE: ${DB_DATABASE:-app}
      MYSQL_USER: ${DB_USERNAME:-app}
      MYSQL_PASSWORD: ${DB_PASSWORD:-secret}
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql.cnf:/etc/mysql/conf.d/custom.cnf
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD:-secret}

  queue:
    build:
      context: .
      target: vendor
    volumes:
      - .:/app
    command: php artisan queue:work --sleep=3 --tries=3
    environment:
      APP_ENV: local
      DB_HOST: mysql
      REDIS_HOST: redis
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_started

  scheduler:
    build:
      context: .
      target: vendor
    volumes:
      - .:/app
    command: php artisan schedule:work
    depends_on:
      - mysql
      - redis

  mailpit:
    image: axllent/mailpit:latest
    ports:
      - "1025:1025"  # SMTP
      - "8025:8025"  # Web UI

volumes:
  mysql_data:
  redis_data:
```

### Nginx Configuration

```nginx
# docker/nginx.conf
server {
    listen 80;
    server_name localhost;
    root /app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.ht {
        deny all;
    }

    # Static assets caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|webp)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Docker Dev Workflow

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f app

# Run artisan commands
docker compose exec app php artisan migrate

# Run tests
docker compose exec app php artisan test

# Install new dependency
docker compose exec app composer require laravel/sanctum

# Rebuild after composer.json changes
docker compose build app
docker compose up -d

# Stop everything
docker compose down

# Destroy volumes (reset DB)
docker compose down -v
```

---

## 16.3 Kubernetes Deployment

### Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                   Kubernetes Cluster                    │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Ingress      │  │  Ingress      │  │  Ingress      │  │
│  │  Controller   │  │  Controller   │  │  Controller   │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
│         │                 │                 │           │
│  ┌──────┴───────┐  ┌──────┴───────┐  ┌──────┴───────┐  │
│  │  Service      │  │  Service      │  │  Service      │  │
│  │  (nginx)      │  │  (app)        │  │  (queue)      │  │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │
│         │                 │                 │           │
│  ┌──────┴───────┐  ┌──────┴───────┐  ┌──────┴───────┐  │
│  │  Pod          │  │  Pod          │  │  Pod          │  │
│  │  (nginx)      │  │  (PHP-FPM)    │  │  (PHP CLI)    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │  PersistentVolume (MySQL + Redis)                │    │
│  └─────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

### Kubernetes Manifests

```yaml
# k8s/namespace.yaml
apiVersion: v1
kind: Namespace
metadata:
  name: php-app
```

```yaml
# k8s/configmap.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: php-app-config
  namespace: php-app
data:
  APP_ENV: production
  APP_DEBUG: "false"
  DB_HOST: mysql-service
  REDIS_HOST: redis-service
```

```yaml
# k8s/secret.yaml
apiVersion: v1
kind: Secret
metadata:
  name: php-app-secrets
  namespace: php-app
type: Opaque
stringData:
  APP_KEY: base64:your-generated-key
  DB_PASSWORD: your-db-password
  REDIS_PASSWORD: your-redis-password
```

```yaml
# k8s/deployment-app.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-app
  namespace: php-app
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
        - name: php-fpm
          image: registry.example.com/php-app:v1.0.0
          ports:
            - containerPort: 9000
          envFrom:
            - configMapRef:
                name: php-app-config
            - secretRef:
                name: php-app-secrets
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
                - -r
                - "echo 'ready';"
            initialDelaySeconds: 5
            periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: php-app-service
  namespace: php-app
spec:
  selector:
    app: php-app
  ports:
    - port: 9000
      targetPort: 9000
```

```yaml
# k8s/deployment-nginx.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: nginx
  namespace: php-app
spec:
  replicas: 2
  selector:
    matchLabels:
      app: nginx
  template:
    metadata:
      labels:
        app: nginx
    spec:
      containers:
        - name: nginx
          image: nginx:alpine
          ports:
            - containerPort: 80
          volumeMounts:
            - name: nginx-config
              mountPath: /etc/nginx/conf.d
      volumes:
        - name: nginx-config
          configMap:
            name: nginx-config
---
apiVersion: v1
kind: Service
metadata:
  name: nginx-service
  namespace: php-app
spec:
  type: LoadBalancer
  selector:
    app: nginx
  ports:
    - port: 80
      targetPort: 80
```

```yaml
# k8s/hpa.yaml (Horizontal Pod Autoscaler)
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: php-app-hpa
  namespace: php-app
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

### Kubernetes Workflow

```bash
# Create namespace and deploy
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secret.yaml
kubectl apply -f k8s/deployment-app.yaml
kubectl apply -f k8s/deployment-nginx.yaml
kubectl apply -f k8s/hpa.yaml

# Check status
kubectl get all -n php-app
kubectl get pods -n php-app -w

# Scale manually
kubectl scale deployment php-app --replicas=5 -n php-app

# Rolling update
kubectl set image deployment/php-app php-fpm=registry.example.com/php-app:v1.0.1 -n php-app
kubectl rollout status deployment/php-app -n php-app

# Rollback
kubectl rollout undo deployment/php-app -n php-app

# Execute command in pod
kubectl exec -it deployment/php-app -n php-app -- php artisan migrate

# View logs
kubectl logs -l app=php-app -n php-app --tail=100 -f

# Port forwarding for debugging
kubectl port-forward service/nginx-service 8080:80 -n php-app

# Delete
kubectl delete ns php-app
```

---

## 16.4 Helm Charts

### Chart Structure

```
php-app-chart/
├── Chart.yaml          # Metadata
├── values.yaml         # Default configuration
├── templates/
│   ├── _helpers.tpl     # Reusable template helpers
│   ├── namespace.yaml
│   ├── configmap.yaml
│   ├── secret.yaml
│   ├── deployment-app.yaml
│   ├── deployment-nginx.yaml
│   ├── service-app.yaml
│   ├── service-nginx.yaml
│   ├── ingress.yaml
│   ├── hpa.yaml
│   └── pvc.yaml
└── charts/             # Sub-charts (mysql, redis)
```

### Chart.yaml

```yaml
apiVersion: v2
name: php-app
description: A production PHP application
type: application
version: 1.0.0
appVersion: 1.0.0
dependencies:
  - name: mysql
    version: "9.0.0"
    repository: "https://charts.bitnami.com/bitnami"
    condition: mysql.enabled
  - name: redis
    version: "17.0.0"
    repository: "https://charts.bitnami.com/bitnami"
    condition: redis.enabled
```

### values.yaml

```yaml
# Application settings
app:
  name: php-app
  image:
    repository: registry.example.com/php-app
    tag: v1.0.0
    pullPolicy: Always
  replicas: 3
  resources:
    requests:
      memory: 128Mi
      cpu: 250m
    limits:
      memory: 512Mi
      cpu: 500m
  env:
    APP_ENV: production
    APP_DEBUG: false
  autoscaling:
    enabled: true
    minReplicas: 3
    maxReplicas: 20
    targetCPUUtilization: 70
    targetMemoryUtilization: 80

# Ingress settings
ingress:
  enabled: true
  host: app.example.com
  tls:
    enabled: true
    secretName: php-app-tls

# Database
mysql:
  enabled: true
  auth:
    database: php_app
    username: php_app
  primary:
    persistence:
      size: 20Gi

# Cache
redis:
  enabled: true
  auth:
    enabled: true
  architecture: standalone
  master:
    persistence:
      size: 5Gi
```

### Using Helm

```bash
# Install with values
helm install php-app ./php-app-chart -f values.yaml

# Upgrade
helm upgrade php-app ./php-app-chart -f values-prod.yaml

# List releases
helm list

# Rollback
helm rollback php-app 1

# Render templates without installing (debug)
helm template ./php-app-chart

# Package for distribution
helm package ./php-app-chart
```

---

## 16.5 CI/CD with Docker

### GitHub Actions Workflow

```yaml
# .github/workflows/deploy.yml
name: Build and Deploy

on:
  push:
    branches: [main]
  release:
    types: [published]

env:
  REGISTRY: ghcr.io
  IMAGE_NAME: ${{ github.repository }}

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: test
        options: --health-cmd="mysqladmin ping" --health-interval=10s
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_mysql, bcmath
      - run: composer install --prefer-dist
      - run: php artisan migrate --env=testing
      - run: php artisan test

  build-and-push:
    needs: test
    runs-on: ubuntu-latest
    permissions:
      contents: read
      packages: write
    steps:
      - uses: actions/checkout@v4
      - name: Log in to registry
        uses: docker/login-action@v3
        with:
          registry: ${{ env.REGISTRY }}
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}
      - name: Build and push
        uses: docker/build-push-action@v5
        with:
          context: .
          push: true
          tags: |
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}:${{ github.sha }}
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}:latest

  deploy:
    needs: build-and-push
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to Kubernetes
        run: |
          echo "${{ secrets.KUBE_CONFIG }}" | base64 --decode > kubeconfig
          helm upgrade --install php-app ./k8s/helm \
            --namespace php-app \
            --set app.image.tag=${{ github.sha }} \
            --kubeconfig kubeconfig
```

---

## 16.6 Monitoring Containerized PHP

### Container Health Checks

```yaml
# In Kubernetes deployment
livenessProbe:
  tcpSocket:
    port: 9000
  initialDelaySeconds: 10
  periodSeconds: 10

readinessProbe:
  exec:
    command:
      - php
      - -r
      - "echo 'ready';"
  initialDelaySeconds: 5
  periodSeconds: 5

# PHP health check endpoint
# public/health.php
<?php
$checks = [
    'php_version' => PHP_VERSION,
    'db' => fn() => DB::connection()->getPdo() ? 'ok' : 'fail',
    'redis' => fn() => Redis::connection()->ping() ? 'ok' : 'fail',
    'storage' => fn() => is_writable(storage_path()) ? 'ok' : 'fail',
];

$status = 200;
foreach ($checks as $name => $check) {
    $result[$name] = is_callable($check) ? $check() : $check;
    if ($result[$name] === 'fail') {
        $status = 503;
    }
}

http_response_code($status);
echo json_encode($result);
```

### Logging in Containers

```php
<?php
// Always log to stdout/stderr in containers
// Laravel: set LOG_CHANNEL=stderr in .env
// Symfony: monolog channels to php://stderr

// Custom logger for containerized environments
class ContainerLogger
{
    public function log(string $level, string $message, array $context = []): void
    {
        $entry = json_encode([
            'timestamp' => date('c'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'service' => gethostname(),
        ]);

        // Structured JSON logging to stdout
        file_put_contents('php://stdout', $entry . PHP_EOL, FILE_APPEND);
    }
}
```

---

## 16.7 Exercises

1. Create a Dockerfile for a PHP application with multi-stage builds (vendor + assets + production)
2. Set up docker-compose with PHP-FPM, Nginx, MySQL 8.0, Redis 7, and Mailpit
3. Configure OPcache and PHP-FPM for production in Docker
4. Deploy a PHP application to a local Kubernetes cluster (minikube or kind)
5. Create a Helm chart for the application with configurable replica counts and resource limits
6. Set up Horizontal Pod Autoscaler based on CPU and memory utilization
7. Configure CI/CD pipeline with GitHub Actions that builds, pushes to registry, and deploys to K8s
8. Implement liveness and readiness probes for PHP-FPM
9. Set up structured JSON logging and view logs with `kubectl logs`
10. Perform a rolling update with zero downtime and rollback on failure
11. Benchmark container startup times: single-stage vs multi-stage builds
12. Configure nginx ingress with TLS termination

---

## Further Reading

- **Doc:** [Docker PHP](https://hub.docker.com/_/php)
- **Doc:** [Kubernetes Documentation](https://kubernetes.io/docs/)
- **Doc:** [Helm Documentation](https://helm.sh/docs/)
- **Doc:** [Docker Compose](https://docs.docker.com/compose/)
- **Book:** "Kubernetes in Action" by Marko Luksa
- **Book:** "Docker Deep Dive" by Nigel Poulton
- **Tool:** [Laravel Octane with Swoole/Scheduler](https://laravel.com/docs/octane)
