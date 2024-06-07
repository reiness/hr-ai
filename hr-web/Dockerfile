# Laravel Dockerfile

# Use an official PHP runtime as a parent image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

# Install Laravel dependencies
RUN composer install

# Make sure permissions are set correctly
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
