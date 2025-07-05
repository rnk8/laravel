FROM php:8.2-apache

# 1. Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# 2. Configura Apache
RUN a2enmod rewrite && \
    sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 3. Configura Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Configura el entorno de trabajo
WORKDIR /var/www/html

# 5. Copia solo los archivos de composer primero para cachear esta capa
COPY composer.json composer.lock ./

# 6. Instala dependencias con cacheo óptimo
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 7. Copia el resto de los archivos
COPY . .

# 8. Configura permisos
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# 9. Genera key de aplicación si no existe
RUN if [ ! -f .env ]; then \
        cp .env.example .env && \
        php artisan key:generate; \
    fi

# 10. Expone el puerto y ejecuta
EXPOSE 80
CMD ["apache2-foreground"]