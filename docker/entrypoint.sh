#!/bin/sh
set -e

PORT=${PORT:-10000}
echo "Configuring Nginx to listen on port $PORT..."
sed "s/PORT_PLACEHOLDER/$PORT/g" /etc/nginx/templates/default.conf > /etc/nginx/http.d/default.conf

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Automatic migrations disabled for production data safety
echo "Data protection mode enabled: skipping automatic database migrations."

# Optimize Laravel for production
echo "Caching Laravel configuration and routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx on port $PORT..."
exec nginx -g "daemon off;"
