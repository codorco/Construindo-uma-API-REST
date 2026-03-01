FROM php:8.2-apache

# Instala e habilita as extensões PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql