# =============================================
# Dockerfile untuk Project Laravel - jtiport
# =============================================

# Base image: PHP 8.3 dengan FPM
FROM php:8.3-fpm

# Set working directory sesuai nama project
WORKDIR /var/www/html

# =============================================
# Install dependencies sistem yang dibutuhkan Laravel & Filament
# =============================================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && \
    # 1. Konfigurasi dan Install ekstensi PHP (Termasuk INTL untuk Filament)
    docker-php-ext-configure intl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache intl && \
    # 2. Install Redis extension agar bisa terhubung ke service Redis
    pecl install redis && \
    docker-php-ext-enable redis && \
    # Bersihkan cache agar ukuran image lebih kecil
    apt-get clean && rm -rf /var/lib/apt/lists/*

# =============================================
# Install Composer
# =============================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =============================================
# Copy seluruh kode project ke dalam container
# =============================================
COPY . /var/www/html

# =============================================
# Fix Git Safe Directory & Permissions
# =============================================
# Menangani error "dubious ownership" agar Composer/Git bisa berjalan lancar
RUN git config --global --add safe.directory /var/www/html && \
    # Setting permission agar Laravel bisa menulis file (log, cache, dll)
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache

# =============================================
# Install dependencies Laravel via Composer
# =============================================
# Menjalankan sebagai user www-data untuk keamanan terbaik
USER www-data

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Balik lagi ke root untuk menjalankan php-fpm
USER root

# Expose port PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
