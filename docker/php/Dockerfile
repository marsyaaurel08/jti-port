FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# =========================
# System dependencies
# =========================
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    icu-dev oniguruma-dev libzip-dev \
    bash

# =========================
# PHP extensions
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath gd intl zip

# =========================
# Composer (ambil binary saja)
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# Copy dependency files dulu (CACHE OPTIMIZATION)
# =========================
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# =========================
# Copy project
# =========================
COPY . .

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# =========================
# Entrypoint
# =========================
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
