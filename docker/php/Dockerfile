# =====================================================================
# STAGE 1: Node — build frontend assets
# =====================================================================
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# =====================================================================
# STAGE 2: PHP — build composer + compile extensions
# =====================================================================
FROM php:8.3-fpm-alpine AS builder
WORKDIR /var/www/html

RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    icu-dev oniguruma-dev libzip-dev \
    autoconf automake libtool make gcc g++ libc-dev

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# =====================================================================
# STAGE 3: Final — runtime only, tidak ada build tools
# =====================================================================
FROM php:8.3-fpm-alpine AS final
WORKDIR /var/www/html

# Hanya runtime dependencies
RUN apk add --no-cache \
    git curl zip unzip bash \
    netcat-openbsd dcron \
    libpng libjpeg-turbo freetype \
    icu-libs oniguruma libzip

# Copy PHP extensions dari builder (bukan compile ulang)
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy vendor dari builder
COPY --from=builder /var/www/html/vendor ./vendor

# Copy frontend assets dari stage node
COPY --from=frontend /app/public/build ./public/build

# Copy project (tanpa node_modules, tanpa build tools)
COPY . .

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
