FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libonig-dev libzip-dev unzip zip curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia los archivos del proyecto
COPY . /var/www/html/

# Cambia permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilita mod_rewrite para Laravel
RUN a2enmod rewrite

# Configura Apache para Laravel (activación de .htaccess)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Expone puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
