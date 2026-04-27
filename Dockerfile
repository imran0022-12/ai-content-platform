FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN mkdir -p storage/framework/sessions
RUN mkdir -p storage/framework/views
RUN mkdir -p storage/framework/cache
RUN chmod -R 775 storage
RUN chmod -R 775 bootstrap/cache

# Expose port
EXPOSE 8080

# Start server
CMD php artisan serve --host=0.0.0.0 --port=8080
