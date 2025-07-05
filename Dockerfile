FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libonig-dev libzip-dev unzip zip curl git \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia los archivos del proyecto
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Ejecuta composer install
RUN composer install --no-dev --optimize-autoloader

# Cambia el DocumentRoot a 'public'
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && sed -i '/<VirtualHost \*:80>/a <Directory "/var/www/html/public">\n    AllowOverride All\n    Require all granted\n</Directory>' /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite
RUN a2enmod rewrite

# Permisos necesarios
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
