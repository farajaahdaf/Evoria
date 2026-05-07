FROM php:8.4-cli

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
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

# Set working directory
WORKDIR /var/www

# Copy composer files first
COPY composer.json composer.lock ./

# Composer install
ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --ignore-platform-reqs

# Copy project files
COPY . .

# Install frontend deps & build Vite
RUN npm install
RUN npm run build

# Remove unnecessary files
RUN rm -rf node_modules

# Prepare Laravel folders
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Permissions
RUN chmod -R 777 storage bootstrap/cache

# Clear Laravel caches safely
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true

# Cache config for production
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Expose Railway port
EXPOSE 8080

# Start app
CMD ["sh", "-c", "cd /var/www && chmod -R 777 storage bootstrap/cache && php artisan migrate --force || true && php artisan storage:link || true && php -S 0.0.0.0:8080 -t public"]
