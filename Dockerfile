FROM php:8.3-fpm

# Instalar o instalador super rápido de extensões PHP pré-compiladas
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Instalar dependências básicas do sistema e Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx

# Instalação rápida e pré-compilada das extensões do PHP (sem estourar timeout)
RUN install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd

# Adicionar usuário sail
RUN useradd -ms /bin/bash sail

# Copiar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www

# Copiar projeto
COPY . /var/www

# Instalar dependências sem pacotes de desenvolvimento
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Configuração do Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]