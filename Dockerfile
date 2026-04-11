//FROM php:8.2-apache
//RUN docker-php-ext-install mysqli pdo pdo_mysql
//COPY . /var/www/html/
//RUN a2enmod rewrite
FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_pgsql

COPY . /var/www/html/
