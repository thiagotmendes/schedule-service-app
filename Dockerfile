FROM php:8.3-fpm

# Instala dependências de sistema
RUN apt-get update && apt-get install -y \
    netcat-openbsd \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libssl-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

# Instala e habilita a extensão phpredis
RUN pecl install redis && docker-php-ext-enable redis

# Instala Composer (via imagem oficial como fonte)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia todos os arquivos da aplicação
COPY . .

COPY commands/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

# Permissões
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expõe a porta (caso queira usar php -S localhost)
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
