# Imagen base oficial con PHP 8.2 y Apache
FROM php:8.2-apache

# Instala extensiones necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip

# Habilita mod_rewrite de Apache
RUN a2enmod rewrite

# Instala Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto
COPY . .

# Instala dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Crea cachés de configuración para producción
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache || true

# Cambia el DocumentRoot a /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && sed -i '/<VirtualHost \*:80>/a <Directory "/var/www/html/public">\n    AllowOverride All\n    Require all granted\n</Directory>' /etc/apache2/sites-available/000-default.conf

# Asegura permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto por defecto de Apache
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
