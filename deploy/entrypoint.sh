#!/bin/sh
set -e

echo "Starting Laravel application..."

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear & cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start PHP-FPM di background
php-fpm -D

echo "PHP-FPM started..."

# Start Nginx
echo "Starting Nginx..."
nginx -g "daemon off;"