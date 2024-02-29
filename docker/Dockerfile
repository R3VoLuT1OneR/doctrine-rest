# Used for local development
FROM php:8.1-cli-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --no-cache git zip libzip-dev unzip curl-dev
RUN apk add --update linux-headers
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install zip curl
RUN apk del -f .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV PATH="${PATH}:/opt/project/vendor/bin"

# Configurations files
COPY conf.d/error_reporting.ini $PHP_INI_DIR/conf.d/error_reporting.ini
COPY conf.d/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

WORKDIR /opt/project
