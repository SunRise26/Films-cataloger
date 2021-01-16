FROM php:7.4-fpm-alpine

RUN apk update && apk add -u \
    composer \
    icu-dev \
    zlib-dev \
    libzip-dev \
    zip \
    libpng-dev

# install php extentions
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-install \
        bcmath \
        pdo_mysql \
        intl \
        opcache \
        gd \
        zip \
    && docker-php-ext-enable xdebug

RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www/webbylab-test

CMD ["php-fpm"]

RUN echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini
