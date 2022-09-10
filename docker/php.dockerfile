FROM php:8.1-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
      libonig-dev \
      libzip-dev \
    && rm -r /var/lib/apt/lists/* \
    && docker-php-ext-install exif \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-install \
      mbstring \
      pcntl \
      pdo_mysql

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libssl-dev zlib1g-dev curl git unzip netcat \
    libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

USER $user