# You may find this Dockerfile useful in development or production
# From the CiviProxy directory
# See docs/installation.md for instructions

FROM composer AS build

COPY . /app
RUN composer install
RUN composer dump-autoload

FROM php:8-apache

COPY --from=build /app/proxy /var/www/html
COPY --from=build /app/vendor /var/www/vendor
COPY --from=build /app/plugins /var/www/plugins
