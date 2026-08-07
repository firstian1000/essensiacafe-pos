FROM dunglas/frankenphp:latest-alpine

# Set working directory
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    freetype-dev \
    zip \
    bash

# Install PHP extensions using the helper provided in FrankenPHP
RUN install-php-extensions \
    gd \
    pcntl \
    bcmath \
    pdo_mysql \
    pdo_pgsql \
    zip \
    opcache

# Copy project files
COPY . .

# Set up environment and permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy Composer from the official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-progress

# Expose dynamic port for Cloud Run and start the server
CMD ["sh", "-c", "frankenphp php-server --listen :$PORT"]
