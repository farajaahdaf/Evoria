# Stage 1: Build frontend assets
FROM node:20-slim AS node-build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# Stage 2: PHP application
FROM php:8.4-cli

# Install system dependencies + PHP extensions (no nodejs/npm)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first for layer caching
COPY composer.json composer.lock ./

ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --ignore-platform-reqs \
    --no-scripts

# Copy project files
COPY . .

# Copy built frontend assets from node stage
COPY --from=node-build /app/public/build ./public/build

# Run post-install scripts now that artisan exists
RUN php artisan package:discover --ansi || true

# Prepare Laravel folders & permissions
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Cache routes and views at build time (no env vars needed)
RUN php artisan route:clear || true \
    && php artisan view:clear || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

EXPOSE 8080

# config:cache runs at runtime so it picks up actual env vars (DB_HOST, etc.)
CMD ["sh", "-c", "chmod -R 777 storage bootstrap/cache && php artisan config:cache && php artisan migrate --force && php artisan storage:link || true && php -S 0.0.0.0:8080 -t public"]
