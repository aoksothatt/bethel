# ========================================================
# Stage 1: Build Vue 3 Frontend
# ========================================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --prefer-offline --no-audit
COPY . .
RUN npm run build

# ========================================================
# Stage 2: Production PHP 8.3 & Nginx Environment
# ========================================================
FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    curl \
    ca-certificates \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    mysql-client

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy backend application
COPY backend/ /var/www/html/

# Copy built frontend assets from Stage 1 into Laravel public
COPY --from=frontend-builder /app/dist/ /var/www/html/public/

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Nginx template & entrypoint
RUN mkdir -p /etc/nginx/templates
COPY docker/nginx.conf /etc/nginx/templates/default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Fix storage & cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
