FROM php:8.3-cli

# Install required system packages and Node.js (for Tailwind/Vite)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install PHP extensions required by Laravel & MySQL
RUN docker-php-ext-install pdo_mysql zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /app

# Copy all project files into the container
COPY . /app

# Install Composer dependencies, NPM packages, and build assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Boot the application using Render's dynamic port
CMD php artisan serve --host=0.0.0.0 --port=$PORT