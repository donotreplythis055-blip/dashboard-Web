FROM php:8.2-apache

# Apache MPM fix
RUN a2dismod mpm_event && a2enmod mpm_prefork

# PostgreSQL driver
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY . /var/www/html/
