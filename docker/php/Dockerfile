FROM php:8.3-fpm

WORKDIR /var/www/html

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache intl && \
    pecl install redis && docker-php-ext-enable redis && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy kode project
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Menjalankan dekripsi env sebelum FPM
CMD sh -c "php artisan env:decrypt --env=production --key=$LARAVEL_ENV_ENCRYPTION_KEY --filename=.env && php-fpm"
