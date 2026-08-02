FROM php:8.3-fpm

# Instalação rápida de extensões PHP pré-compiladas
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Dependências mínimas do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nginx

# Instalação rápida das extensões
RUN install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd

# Copiar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar os arquivos do projeto (incluindo o front-end já compilado pela Action)
COPY . /var/www

# Instalar dependências do PHP sem pacotes de dev
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Configuração do Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]