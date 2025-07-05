#!/bin/bash
set -e

# Instala dependencias
composer install --no-interaction --optimize-autoloader

# Cache de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache