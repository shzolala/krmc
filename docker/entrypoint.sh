#!/bin/sh
set -e

echo "Starting application setup..."

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Set APP_KEY in the environment variables (e.g. in Coolify) and redeploy." >&2
    exit 1
fi

fix_permissions() {
    chown -R app:www-data storage bootstrap/cache
    chmod -R g+rwX storage bootstrap/cache
}

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

fix_permissions

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Running database migrations..."
php artisan migrate --force

php artisan storage:link --force

# Artisan runs as root; make anything it created writable by PHP-FPM (www-data)
fix_permissions

echo "Application ready!"

exec "$@"
