# You may find this Dockerfile useful in development or production
# From the CiviProxy directory
# * Build a docker image with `docker build . -t civiproxy`
# * Run a development container with `run -d -p 4050:80 -v $PWD/proxy:/var/www/html --name civiproxy civiproxy`

FROM php:7-apache

COPY proxy/ /var/www/html
