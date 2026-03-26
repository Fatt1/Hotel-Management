FROM php:8.3-cli

# System packages for Laravel + Node/Vite
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app source
COPY . .

# Ensure Laravel runtime/cache directories exist inside the image.
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Install PHP and Node dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install

EXPOSE 8000 5173

# Start web server + Vite dev server + queue worker in one container
CMD ["sh", "-lc", "php artisan migrate --seed --force || true; php artisan serve --host=0.0.0.0 --port=8000 & npm run dev -- --host 0.0.0.0 --port 5173 & php artisan queue:work database --queue=emails,default --tries=3 --timeout=120"]
