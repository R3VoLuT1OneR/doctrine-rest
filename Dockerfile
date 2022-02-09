# Used for local development
FROM php:8.0-cli-alpine

USER root

RUN apk add git zip unzip libzip-dev curl-dev && docker-php-ext-install zip curl
RUN apk del gcc g++ && rm -rf /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV PATH="${PATH}:/opt/project/vendor/bin"

USER www-data
WORKDIR /opt/project
