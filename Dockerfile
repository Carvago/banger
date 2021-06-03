
FROM composer:latest as build-prod

WORKDIR /app/

COPY composer.* ./

RUN composer install --no-dev

FROM build-prod as build-dev

RUN composer install

FROM php:8.0-cli as php-dev

RUN docker-php-ext-install bcmath && \
    pecl install pcov && \
    docker-php-ext-enable pcov

WORKDIR /app/

COPY --from=build-dev /app/vendor /var/www/vendor
COPY . /app