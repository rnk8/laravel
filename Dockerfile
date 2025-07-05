# Stage 1: Build assets with Node.js
FROM node:20 AS build

WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install

# Copy Vite config and assets
COPY vite.config.js ./
COPY resources/ ./resources/

# Build production assets
RUN npm run build

# Stage 2: Production image
FROM php:8.2-apache

# 1. Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# 2. Configura Apache correctamente
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 3. Configura Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Copia archivos de recursos
COPY resources/ ./resources/
# 4. Directorio de trabajo
WORKDIR /var/www/html

# 5. Copia archivos necesarios
COPY composer.json composer.lock ./

# 6. Instala dependencias
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 7. Copia toda la aplicación
COPY . .

# 8. Copia los assets compilados desde la etapa de construcción
COPY --from=build /app/public/build /var/www/html/public/build

# 9. Configura permisos
RUN chown -R www-data:www-data storage bootstrap/cache public/build && \
    chmod -R 775 storage bootstrap/cache public/build

# 10. Prepara la aplicación
RUN php artisan config:clear && \
    php artisan cache:clear

# 11. Comando de inicio optimizado
CMD ["bash", "-c", "php artisan config:cache && php artisan view:cache && php artisan migrate --force && apache2-foreground"]
EXPOSE 80