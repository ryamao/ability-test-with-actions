FROM php:8.3.1-fpm

SHELL ["/bin/bash", "-eux", "-o", "pipefail", "-c"]

WORKDIR /var/www/html

ENV TZ="Asia/Tokyo"

RUN apt-get update; \
    apt-get install -y default-mysql-client zlib1g-dev libzip-dev unzip; \
    docker-php-ext-install pdo_mysql zip; \
    curl -sS https://getcomposer.org/installer | php; \
    mv composer.phar /usr/local/bin/composer; \
    composer self-update

COPY php.ini /usr/local/etc/php/

EXPOSE 9000

