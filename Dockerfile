FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libonig-dev libzip-dev unzip zip curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia todos los archivos del proyecto
COPY . /var/www/html/

# Establece la raíz del documento en la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Actualiza la configuración de Apache
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Habilita mod_rewrite para Laravel
RUN a2enmod rewrite

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
