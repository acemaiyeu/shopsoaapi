# Base image with PHP 8.3
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# Install Composer 2.8.6
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.8.6 && \
    mv composer.phar /usr/local/bin/composer && \
    composer --version

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose port (default Laravel port if using artisan serve)
EXPOSE 8000

# Start PHP-FPM
CMD ["php-fpm"]
