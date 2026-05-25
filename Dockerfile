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

# Stage 3: Production image (Debian-based — lebih stabil dari Alpine)
FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    $PHPIZE_DEPS \
    nginx \
    supervisor \
    default-mysql-client \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get purge -y $PHPIZE_DEPS \
    && apt-get autoremove -y \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=vendor /app/vendor ./vendor
COPY --from=node-build /app/public/build ./public/build
COPY . .

RUN php artisan package:discover --ansi 2>/dev/null || true

RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php-fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# OPcache tuning untuk low-memory instance
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=64\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=4000\n\
opcache.revalidate_freq=0\n\
opcache.save_comments=1\n\
opcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80
CMD ["/start.sh"]
