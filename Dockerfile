FROM php:8.3-fpm

# 1. Copiar o instalador de binários pré-compilados
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 2. Pacotes base do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx

# 3. Instalar extensões usando APENAS os binários prontos (leva menos de 10 segundos)
RUN install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd

RUN useradd -ms /bin/bash sail

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]