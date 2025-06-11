#!/bin/bash

# Aguarda o MySQL estar disponível
until nc -z mysql 3306; do
  echo "⏳ Aguardando MySQL..."
  sleep 2
done

# Aguarda volume montar corretamente
sleep 5

# Instala dependências do PHP
composer install --no-interaction --prefer-dist --optimize-autoloader

# Copia o .env.example para .env caso ainda não exista
if [ ! -f ".env" ]; then
  cp .env.example .env
  php artisan key:generate
fi

# Roda as migrations
php artisan migrate --force

# (Opcional) Roda os seeders
php artisan db:seed --force

php artisan serve --host=0.0.0.0 --port=8000
