
FROM composer:latest as build-prod

WORKDIR /app/

COPY composer.* ./

RUN composer install --no-dev --ignore-platform-reqs --no-suggest --no-progress

FROM build-prod as build-dev

RUN composer install --ignore-platform-reqs --no-suggest --no-progress

FROM php:8.0-cli as php-dev

RUN docker-php-ext-install bcmath && \
    pecl install pcov && \
    docker-php-ext-enable pcov

WORKDIR /app/

COPY --from=build-dev /app/vendor /var/www/vendor
COPY . /app