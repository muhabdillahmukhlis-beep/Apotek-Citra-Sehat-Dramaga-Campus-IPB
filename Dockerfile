FROM php:8.2-cli

# 1. Install dependensi sistem & ekstensi PHP untuk Laravel
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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Setup working directory
WORKDIR /var/www/html
COPY . .

# 3. Install Composer & dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 4. Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Jalankan internal web server Laravel langsung mengarah ke port Railway
EXPOSE 80

# Set izin akses folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# TAMBAHKAN BARIS INI: Buat file database kosong untuk SQLite
RUN touch /var/www/html/storage/database.sqlite

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]