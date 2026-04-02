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
    && npm install \
    && npm run build

EXPOSE 8000

# Start web server + queue worker. Frontend assets are served from public/build.
CMD ["sh", "-lc", "rm -f public/hot; php artisan storage:link --force || true; php artisan migrate --force || true; php artisan queue:work database --queue=emails,default --tries=3 --timeout=120 & php artisan serve --host=0.0.0.0 --port=8000"]
