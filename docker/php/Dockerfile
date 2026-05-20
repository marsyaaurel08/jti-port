# ==============================================================================
# STAGE 1: FRONTEND BUILDER (Node.js)
# Hanya bertugas melakukan npm install & build, setelah itu dibuang.
# ==============================================================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy file konfigurasi npm terlebih dahulu (optimasi cache)
COPY package.json package-lock.json* ./
RUN npm install

# Copy seluruh source code dan lakukan build (Vite/Mix)
COPY . .
RUN npm run build


# ==============================================================================
# STAGE 2: PRODUCTION RUNTIME (PHP-FPM)
# Image final yang sangat ringan, tanpa Node.js dan tanpa C++ Compiler
# ==============================================================================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 1. Instal dependencies sistem dasar
RUN apk add --no-cache \
    git curl zip unzip bash netcat-openbsd dcron \
    libpng-dev libjpeg-turbo-dev freetype-dev icu-dev oniguruma-dev libzip-dev \
    # 2. Instal compiler di dalam "virtual package" bernama .build-deps
    && apk add --no-cache --virtual .build-deps \
       autoconf automake libtool make gcc g++ libc-dev \
    # 3. Lakukan compile PECL dan ekstensi PHP
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable opcache \
    # 4. HAPUS seluruh compiler dari sistem setelah selesai digunakan! (Penting)
    && apk del .build-deps

# =========================
# PHP-FPM & OPcache config
# =========================
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies backend
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy seluruh code Jtiport
COPY . .

# =========================
# Injeksi Aset Frontend dari STAGE 1
# =========================
# Kita hanya mengambil folder public (hasil build CSS/JS) dari kontainer Node
COPY --from=frontend-builder /app/public ./public

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# =========================
# Entrypoint (Fix CRLF ke LF)
# =========================
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
