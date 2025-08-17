# ---- Base Image ----
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    netcat-traditional \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install dependencies (without dev)
RUN composer install --optimize-autoloader --no-dev

# Copy wait script
COPY wait-for-db.sh /var/www/wait-for-db.sh
RUN chmod +x /var/www/wait-for-db.sh

# Expose port
EXPOSE 8000

# Start with wait-for-db script
CMD ["/var/www/wait-for-db.sh"]
