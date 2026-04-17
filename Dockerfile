FROM dunglas/frankenphp:latest-php8.3

WORKDIR /app

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer les extensions PHP nécessaires
RUN install-php-extensions \
    pdo_mysql \
    intl \
    opcache \
    mbstring \
    zip

# Copier et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copier le reste de l'application
COPY . .
RUN composer dump-autoload --optimize --no-dev
RUN mkdir -p var && chmod -R 777 var

CMD ["sh", "start.sh"]