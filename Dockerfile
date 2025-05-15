FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install pcov && docker-php-ext-enable pcov

RUN echo "pcov.enabled=1" > /usr/local/etc/php/conf.d/99-pcov.ini

COPY --from=composer/composer:latest-bin /composer /usr/local/bin/composer

WORKDIR /app