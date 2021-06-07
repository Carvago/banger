FROM php:8.0-cli as php-dev

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions bcmath intl pcov zip

WORKDIR /app/

FROM php-dev as composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
