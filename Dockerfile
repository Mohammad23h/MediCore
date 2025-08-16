# استخدام PHP 8.2 مع Apache
FROM php:8.2-apache

# تثبيت مكتبات يحتاجها Laravel
RUN docker-php-ext-install pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط مجلد العمل
WORKDIR /var/www/html

# تثبيت مكتبات Laravel
RUN composer install --optimize-autoloader --no-dev

# تهيئة Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# إعداد مجلدات Laravel للكتابة
RUN chmod -R 775 storage bootstrap/cache

# تعيين Apache ليخدم من مجلد public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
CMD ["apache2-foreground"]
