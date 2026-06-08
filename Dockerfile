FROM php:8.2-apache

# 1. Install dependensi sistem & ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Aktifkan modul rewrite Apache untuk routing Laravel (.htaccess)
RUN a2enmod rewrite

# 3. Set direktori kerja utama di dalam container
WORKDIR /var/www/html
COPY . .

# 4. Salin file konfigurasi virtualhost kita
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 5. Pasang Composer & package dependensi Laravel secara optimal
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6. Set izin akses folder storage & bootstrap agar tidak error permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80