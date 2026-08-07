#!/bin/bash
set -e

# Ensure storage subdirectories exist
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Ensure database directory and SQLite file exist
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Ensure full permissions
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations and seeders
php artisan migrate --force || true
php artisan db:seed --force || true

# Create storage symlink for public assets
php artisan storage:link --force || true

# Clear cache to reflect env
php artisan config:clear || true
php artisan cache:clear || true

# Start Apache in foreground
exec apache2-foreground
