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

# 2. Enable Apache rewrite module untuk routing Laravel (.htaccess)
RUN a2enmod rewrite

# 3. Setup working directory
WORKDIR /var/www/html
COPY . .

# 4. Ganti konfigurasi default Apache dengan file config kita yang baru
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 5. Install Composer & dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6. Set proper permissions untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80