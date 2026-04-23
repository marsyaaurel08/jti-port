FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# =========================
# System dependencies
# FIX: Tambah netcat-openbsd (nc) untuk health check DB di entrypoint.
#      Tanpa ini, `nc -z db 3306` di entrypoint akan gagal.
# =========================
RUN apk add --no-cache \
    git curl zip unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    icu-dev oniguruma-dev libzip-dev \
    bash \
    netcat-openbsd

# =========================
# PHP extensions
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable opcache

# =========================
# PHP-FPM & OPcache config
# =========================
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# Install dependencies dulu (cache layer optimization)
# =========================
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# =========================
# Copy seluruh project
# =========================
COPY . .

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# =========================
# Entrypoint
# FIX: Copy dari path yang konsisten dengan lokasi di repo
# =========================
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
