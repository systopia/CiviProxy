# Build: `docker build . -t civiproxy`
# Run: `docker run -d -p 4050:4050 --name civiproxy civiproxy`
# Browse: https://localhost:4050

# This is a multi-stage build file. See https://docs.docker.com/develop/dev-best-practices/

# Generate SSL/TLS cert and key.
FROM debian:buster-slim AS cert_builder
RUN apt update && apt install -y openssl
RUN sed -i 's/^# subjectAltName=email:copy/subjectAltName=DNS:localhost/g' /etc/ssl/openssl.cnf
RUN /usr/bin/openssl req \
-subj '/CN=localhost/O=CiviProxyDev/C=UK' \
-nodes \
-new \
-x509 \
-newkey rsa:2048 \
-keyout /etc/ssl/certs/civiproxy.key \
-out /etc/ssl/certs/civiproxy.crt \
-days 1095

# Stand up CiviProxy
FROM php:7-apache
COPY --from=cert_builder /etc/ssl/certs/ /etc/ssl/certs/
COPY proxy/ /var/www/html
COPY civiproxy.ssl.conf /etc/apache2/sites-available/
RUN a2enmod ssl
RUN service apache2 restart
RUN a2dissite 000-default.conf
RUN a2dissite default-ssl.conf
RUN a2ensite civiproxy.ssl.conf
EXPOSE 4050
