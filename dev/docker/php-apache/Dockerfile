# arguments & versions
ARG PHP_VERSION=7.4.27-apache
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

RUN set -ex; \
    docker-php-ext-install -j "$(nproc)" \
        bcmath \
        zip \
    ;

# https://wordpress.org/support/article/editing-wp-config-php/#configure-error-logging
RUN { \
# https://www.php.net/manual/en/errorfunc.constants.php
# https://github.com/docker-library/wordpress/issues/420#issuecomment-517839670
        echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
        echo 'display_errors = Off'; \
        echo 'display_startup_errors = Off'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
        echo 'log_errors_max_len = 1024'; \
        echo 'ignore_repeated_errors = On'; \
        echo 'ignore_repeated_source = Off'; \
        echo 'html_errors = Off'; \
        echo 'upload_max_filesize = 1500 M'; \
        echo 'max_input_time = 1000'; \
        echo 'memory_limit = 640M'; \
        echo 'max_execution_time = 1800'; \
        echo 'post_max_size = 2000 M'; \
    } > /usr/local/etc/php/conf.d/error-logging.ini

RUN a2enmod rewrite headers mime actions mime cgi include

# install composer
ARG COMPOSER_VERSION
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
    && chmod +x /usr/local/bin/composer

# copy env files
COPY ./docker/php-apache/vhost.conf /etc/apache2/sites-available/000-default.conf
