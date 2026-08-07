FROM php:8.3-apache

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libsqlite3-dev \
    sqlite3 \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache DocumentRoot to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Default Environment Variables
ENV APP_ENV=production
ENV APP_DEBUG=true
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/database/database.sqlite
ENV APP_KEY=base64:7vFwV8h6aN+wK1Q6XG8Yy1bY9Pz+7z/x0N5k9j5a8B4=

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer PHP dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies & build frontend assets via Vite
RUN npm install && npm run build

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Set permissions
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 80

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
