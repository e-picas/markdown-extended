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
        openssh-client \
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

# install nvm
ENV NODE_VERSION v21.6.1
ENV NVM_DIR /usr/local/nvm
RUN mkdir $NVM_DIR \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.3/install.sh | bash
RUN echo "source $NVM_DIR/nvm.sh && \
    nvm install $NODE_VERSION && \
    nvm alias default $NODE_VERSION && \
    nvm use default" | bash
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN echo "source /root/.bashrc \
    &&  npm install -g \
        semantic-release \
        @semantic-release/github \
        @semantic-release/git \
        @semantic-release/changelog \
        @semantic-release/exec \
        @saithodev/semantic-release-backmerge" | bash

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# install phpdoc
RUN wget -O /usr/local/bin/phpdoc https://github.com/phpDocumentor/phpDocumentor/releases/download/v2.9.1/phpDocumentor.phar \
    && chmod +x /usr/local/bin/phpdoc

RUN addgroup --gid 1000 app_user && adduser --system --uid 1000 --gid 1000 app_user
RUN cp /root/.bashrc /home/app_user/ && chown 1000:1000 /home/app_user/.bashrc
USER app_user
