# =============================================
# Dockerfile untuk Project Laravel - jtiport
# =============================================

# Base image: PHP 8.3 dengan FPM
FROM php:8.3-fpm

# Set working directory sesuai nama project
WORKDIR /var/www/html

# =============================================
# Install dependencies sistem yang dibutuhkan Laravel
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
    && \
    # Install ekstensi PHP yang umum dipakai Laravel
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache && \
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
# Setting permission folder storage dan bootstrap/cache
# Agar Laravel bisa menulis file (log, cache, session, dll)
# =============================================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# =============================================
# Install dependencies Laravel via Composer
# =============================================
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
