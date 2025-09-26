FROM php:8.2-fpm-alpine

RUN apk add --no-cache \ 
    bash git curl zip unzip libzip-dev libpng-dev icu-dev oniguruma-dev openssl-dev autoconf build-base mysql-client

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql bcmath intl opcache

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Configure PHP
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

CMD ["php-fpm", "-F"]

