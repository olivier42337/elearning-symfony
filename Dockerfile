FROM dunglas/frankenphp:latest-php8.3

WORKDIR /app

RUN install-php-extensions \
    pdo_mysql \
    intl \
    opcache \
    mbstring \
    zip

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev
RUN mkdir -p var && chmod -R 777 var

CMD ["sh", "start.sh"]