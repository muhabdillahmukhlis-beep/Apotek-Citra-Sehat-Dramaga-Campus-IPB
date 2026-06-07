FROM php:8.2-apache

# 1. Install system dependencies & PHP extensions
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

# 2. Aktifkan modul rewrite Apache untuk Laravel routing (.htaccess)
RUN a2enmod rewrite

# 3. Tentukan folder kerja utama
WORKDIR /var/www/html
COPY . .

# 4. Salin file virtualhost kita untuk mengarahkan root ke folder public
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 5. Pasang Composer & package dependensi Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6. Atur izin akses folder storage & bootstrap cache agar tidak error permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 7. Jalankan Apache di foreground secara aman menggunakan path biner bawaan
CMD ["apache2-foreground"]