FROM php:8.2-apache

# 1. Configuración inicial
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# 3. Configura Apache
RUN a2enmod rewrite && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 4. Instala Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. Directorio de trabajo
WORKDIR /var/www/html

# 6. Copia archivos de composer primero
COPY composer.json composer.lock ./

# 7. Instala dependencias (ignorando advertencias de versión)
RUN composer install --no-interaction --optimize-autoloader --no-scripts --ignore-platform-req=doctrine/dbal

# 8. Copia el resto de archivos
COPY . .

# 9. Permisos
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# 10. Prepara aplicación
RUN if [ ! -f .env ]; then \
        cp .env.example .env && \
        php artisan key:generate; \
    fi

# 11. Comando final
CMD ["bash", "-c", "php artisan migrate --force && apache2-foreground"]

EXPOSE 80