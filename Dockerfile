FROM php:8.3.9RC1-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN set -ex \
    && apk update \
    && apk --no-cache add postgresql-dev nodejs yarn npm icu-dev zip libzip-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql intl zip

WORKDIR /var/www/html
