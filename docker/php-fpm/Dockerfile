FROM php:8.1-fpm-alpine

RUN apk update && apk upgrade
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql mysqli

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

USER app
