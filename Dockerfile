# Stage 1: Frontend assets
FROM node:20-slim AS node-build
WORKDIR /app
COPY package*.json vite.config.js postcss.config.js tailwind.config.js ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Stage 3: Production image
FROM php:8.4-fpm-alpine

# System dependencies + PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# PHP-FPM config: listen on 127.0.0.1:9000
RUN sed -i 's|listen = /var/run/php-fpm.sock|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's|;listen.owner = nobody|listen.owner = nobody|' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www

# Copy dependencies
COPY --from=vendor /app/vendor ./vendor
COPY --from=node-build /app/public/build ./public/build

# Copy source
COPY . .

# Post-install scripts
RUN php artisan package:discover --ansi 2>/dev/null || true

# Prepare storage
RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx + supervisord config
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]
