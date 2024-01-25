# arguments & versions
ARG PHP_VERSION=7
ARG COMPOSER_VERSION=2.1.12

# base PHP-FPM image
FROM php:${PHP_VERSION}

# update system
RUN set -ex; \
    export DEBIAN_FRONTEND=noninteractive; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        libzip-dev \
        wget \
    ; 

RUN set -ex; docker-php-ext-install -j "$(nproc)" zip;

# install composer
ARG COMPOSER_VERSION
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
    && chmod +x /usr/local/bin/composer
