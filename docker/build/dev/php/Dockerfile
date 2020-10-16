FROM php:7.4-fpm-alpine

COPY --from=composer:1.10.10 /usr/bin/composer /usr/bin/composer

RUN rm -r /var/www/*
WORKDIR /var/www
