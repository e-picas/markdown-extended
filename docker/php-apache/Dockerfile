# base image with PHP & Apache
FROM php:7.4.27-apache

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

RUN { \
# https://www.php.net/manual/en/errorfunc.constants.php
        echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
        echo 'display_errors = On'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
    } > /usr/local/etc/php/conf.d/error-logging.ini

RUN a2enmod rewrite headers mime actions mime cgi include autoindex

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# install phpdoc
RUN wget -O /usr/local/bin/phpdoc https://github.com/phpDocumentor/phpDocumentor/releases/download/v2.9.1/phpDocumentor.phar \
    && chmod +x /usr/local/bin/phpdoc

# copy the virtual host config
COPY ./dev/docker/php-apache/vhost.conf /etc/apache2/sites-available/000-default.conf
