# arguments to adapt the build
ARG IMAGE_VERSION=7

# base image with PHP & Apache
FROM php:${IMAGE_VERSION} AS mde_dev

# install dependencies
RUN set -ex; \
    export DEBIAN_FRONTEND=noninteractive; \
    apt update; \
    apt install -y --no-install-recommends \
        git \
        graphviz \
        libxslt1-dev \
        libxml2-dev \
        libyaml-dev \
        libzip-dev \
        man \
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
RUN echo "phar.readonly = Off" > /usr/local/etc/php/php.ini;

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# install phpdoc
RUN wget -O /usr/local/bin/phpdoc https://github.com/phpDocumentor/phpDocumentor/releases/download/v2.9.1/phpDocumentor.phar \
    && chmod +x /usr/local/bin/phpdoc

# now build with a webserver
FROM mde_dev AS mde_server

RUN { \
# https://www.php.net/manual/en/errorfunc.constants.php
        echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
        echo 'display_errors = On'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
    } > /usr/local/etc/php/conf.d/error-logging.ini

RUN a2enmod rewrite headers mime actions mime cgi include autoindex

# copy the virtual host config
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf
