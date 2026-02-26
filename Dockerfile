FROM dunglas/frankenphp:1-php8.3-alpine

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation des extensions PHP nécessaires pour Symfony & MariaDB
RUN install-php-extensions \
    pdo_mysql \
    intl \
    zip \
    opcache

WORKDIR /app