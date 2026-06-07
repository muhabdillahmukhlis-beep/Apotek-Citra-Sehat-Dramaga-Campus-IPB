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

# 2. Perbaikan Error Konflik MPM Apache
RUN a2dismod mpm_event || true
RUN a2enmod mpm_prefork || true

# 3. Enable Apache rewrite module untuk routing Laravel (.htaccess)
RUN a2enmod rewrite

# 4. Setup working directory
WORKDIR /var/www/html
COPY . .

# 5. Ganti konfigurasi default Apache dengan file config kita
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 6. Install Composer & dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 7. Set proper permissions untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80