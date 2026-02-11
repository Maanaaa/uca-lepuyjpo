FROM dunglas/frankenphp:alpine AS base_runtime

# Installation des extensions PHP
RUN install-php-extensions pdo_mysql intl zip opcache bcmath gd

# Installation de Composer (l'outil magique qui nous manquait)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Node.js et npm
RUN apk add --no-cache nodejs npm

WORKDIR /app

# Stage Symfony
FROM base_runtime AS symfony_dev
CMD ["frankenphp", "run"]

# Stage Next
FROM base_runtime AS next_dev
EXPOSE 3000
CMD ["npm", "run", "dev"]