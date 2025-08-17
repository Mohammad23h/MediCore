#!/bin/sh

# استخدم القيم من ENV أو اعطي قيم افتراضية
HOST="${DB_HOST:-mysql.railway.internal}"
PORT="${DB_PORT:-3306}"

echo "⏳ Waiting for database at $HOST:$PORT..."

# انتظر حتى يفتح البورت
while ! nc -z "$HOST" "$PORT"; do
  sleep 1
done

echo "✅ Database is up!"

# شغل Laravel بعد ما يجهز DB
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=8000
