# Imagen oficial de PHP 8.3 con Apache
FROM php:8.3-apache

# Instalamos la extensión pdo_mysql para conectar con MySQL 8
RUN docker-php-ext-install pdo_mysql

# Apuntamos la raíz web de Apache a public/ para que config.php, includes/ y database/ queden fuera del alcance del navegador
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
