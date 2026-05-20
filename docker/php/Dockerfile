# =====================================================================
# STAGE 1: Builder — composer + npm build (butuh vendor untuk filament)
# =====================================================================
FROM php:8.3-fpm-alpine AS builder
WORKDIR /var/www/html

RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    icu-dev oniguruma-dev libzip-dev \
    autoconf automake libtool make gcc g++ libc-dev \
    nodejs npm

# PHP extensions
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies dulu
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# Install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy semua file (vendor sudah ada, filament CSS bisa di-resolve)
COPY . .

# Build frontend — vendor/filament sudah tersedia
RUN npm run build \
    && rm -rf node_modules  # hapus setelah build

# =====================================================================
# STAGE 2: Final — runtime only
# =====================================================================
FROM php:8.3-fpm-alpine AS final
WORKDIR /var/www/html

RUN apk add --no-cache \
    bash curl zip unzip \
    netcat-openbsd dcron \
    libpng libjpeg-turbo freetype \
    icu-libs oniguruma libzip

# Copy PHP extensions dari builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy hasil build dari builder (vendor + public/build sudah ada)
COPY --from=builder /var/www/html/ .

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
