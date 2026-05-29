#!/bin/sh
set -e

echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Storage link..."
php artisan storage:link 2>/dev/null || true

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
