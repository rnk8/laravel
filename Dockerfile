# Utiliza la imagen oficial de PHP con Apache
FROM php:8.2-apache

# 1. Configuración inicial del sistema
ENV DEBIAN_FRONTEND=noninteractive
RUN echo 'APT::Get::Assume-Yes "true";' > /etc/apt/apt.conf.d/90render

# 2. Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Instala extensiones PHP necesarias
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    bcmath

# 4. Configura Apache
RUN a2enmod rewrite && \
    sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 5. Configura Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Establece el directorio de trabajo
WORKDIR /var/www/html

# 7. Copia solo los archivos de composer primero para optimizar caché
COPY composer.json composer.lock ./

# 8. Instala dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 9. Copia el resto de los archivos de la aplicación
COPY . .

# 10. Configura permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# 11. Prepara la aplicación para producción
RUN if [ ! -f .env ]; then \
        cp .env.example .env && \
        php artisan key:generate; \
    fi && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# 12. Comando para ejecutar migraciones y servir la aplicación
CMD bash -c "php artisan migrate --force && apache2-foreground"

# 13. Expone el puerto HTTP
EXPOSE 80