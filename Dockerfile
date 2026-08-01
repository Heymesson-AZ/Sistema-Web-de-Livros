FROM php:8.3-fpm

# Instalar dependências do sistema e extensões necessárias do PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN useradd -ms /bin/bash sail
# Copiar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
&& apt-get install -y nodejs

WORKDIR /var/www

# Copiar projeto
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default
EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]