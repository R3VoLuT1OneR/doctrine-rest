# Used for local development
FROM keinos/php8-jit:latest

USER root

RUN apk add git zip unzip libzip-dev curl-dev && docker-php-ext-install zip curl
RUN apk del gcc g++ && rm -rf /var/cache/apk/*

# Install composer 2
RUN cd /tmp && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'c31c1e292ad7be5f49291169c0ac8f683499edddcfd4e42232982d0fd193004208a58ff6f353fde0012d35fdd72bc394') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    composer config --global github-protocols https

# Install composer 1
RUN php -r "copy('https://getcomposer.org/composer-1.phar', '/usr/local/bin/composer-1');" && \
    chmod +x /usr/local/bin/composer-1 && \
    composer-1 config --global github-protocols https

USER www-data
WORKDIR /var/www
