FROM php:8.2-apache

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# Habilita mod_rewrite
RUN a2enmod rewrite

# Configura variables para composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copia solo los archivos de composer primero
COPY composer.json composer.lock ./

# Instala dependencias
RUN composer install --no-interaction --optimize-autoloader

# Copia el resto de archivos
COPY . .

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configura Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf && \
    sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf

# Genera key si no existe
RUN if [ ! -f .env ]; then cp .env.example .env && php artisan key:generate; fi

EXPOSE 80
CMD ["apache2-foreground"]