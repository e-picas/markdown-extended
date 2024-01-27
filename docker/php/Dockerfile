# base image PHP 7 latest
FROM php:7

# install dependencies
RUN set -ex; \
    export DEBIAN_FRONTEND=noninteractive; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        graphviz \
        libxslt1-dev \
        libxml2-dev \
        libyaml-dev \
        libzip-dev \
        wget \
    ;
RUN set -ex; \
    docker-php-ext-install -j "$(nproc)" \
        bcmath \
        xsl \
        zip \
    ;
RUN set -ex; pecl install yaml && docker-php-ext-enable yaml;
RUN set -ex; pecl install xdebug-3.1.4 && docker-php-ext-enable xdebug; \
    echo 'xdebug.mode=coverage' > /usr/local/etc/php/conf.d/xdebug.ini;

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# install phpdoc
RUN wget -O /usr/local/bin/phpdoc https://github.com/phpDocumentor/phpDocumentor/releases/download/v2.9.1/phpDocumentor.phar \
    && chmod +x /usr/local/bin/phpdoc
