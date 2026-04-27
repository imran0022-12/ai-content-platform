FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nodejs npm \
    libicu-dev

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

RUN mkdir -p database && touch database/database.sqlite
RUN chmod -R 775 database

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
