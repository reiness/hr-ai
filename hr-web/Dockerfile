# Laravel Dockerfile
FROM php:8.2-cli
WORKDIR /var/www
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \   
    unzip \
    nodejs \
    npm \
    supervisor \
    libzip-dev

RUN docker-php-ext-configure zip
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www

RUN composer install
RUN php artisan storage:link
WORKDIR /var/www

RUN npm install 
RUN npm run build 

RUN mkdir -p /var/www/storage /var/www/storage/app/public /var/www/storage/framework/sessions /var/www/storage/framework/views /var/www/storage/framework/cache
RUN chmod -R 755 /var/www/storage
RUN chown -R www-data:www-data /var/www/storage

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
EXPOSE 8000
CMD ["/usr/bin/supervisord"]
