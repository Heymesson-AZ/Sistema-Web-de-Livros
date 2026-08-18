# syntax=docker/dockerfile:1.7

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize

FROM php:8.3-fpm

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    bcmath \
    exif \
    gd \
    pcntl

WORKDIR /var/www

COPY --from=vendor /app /var/www

COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default

RUN php artisan optimize:clear || true

RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=10s --retries=3 \
CMD curl -f http://localhost || exit 1

EXPOSE 80

CMD ["sh","-c","php-fpm -D && nginx -g 'daemon off;'"]