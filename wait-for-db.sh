#!/bin/sh
set -e

host="$DB_HOST"
port="$DB_PORT"
user="$DB_USERNAME"
password="$DB_PASSWORD"

echo "⏳ Waiting for database at $host:$port..."
until mysqladmin ping -h"$host" -P"$port" -u"$user" -p"$password" --silent; do
  sleep 2
done

echo "✅ Database is up!"

# Clear & cache configs
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Run migrations
php artisan migrate --force

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000
