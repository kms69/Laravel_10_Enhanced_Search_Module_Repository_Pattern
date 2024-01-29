# Build Stage
FROM composer:2.4 as build
COPY . /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Development Stage
FROM php:8.2.3-apache-buster as dev
# Add the ServerName directive
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install required dependencies
RUN apt-get update && apt-get install -y zip libzip-dev libonig-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install the official Elasticsearch PHP client using Composer
COPY --from=build /app /var/www/html/
COPY --from=composer:2.4 /usr/bin/composer /usr/bin/


# Install Composer dependencies
RUN composer install --prefer-dist --no-interaction

# Set environment variables
ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy Apache configuration
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy environment file
COPY .env /var/www/html/.env

# Configure Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite
