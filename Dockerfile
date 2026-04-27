FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nodejs npm sqlite3 libsqlite3-dev

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

RUN mkdir -p database && touch database/database.sqlite
RUN chmod -R 775 database

# Force SQLite extension
RUN docker-php-ext-enable pdo_sqlite

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
