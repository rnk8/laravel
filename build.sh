#!/bin/bash
set -e

# Resuelve dependencias
composer install --no-interaction --optimize-autoloader --no-dev

# Cache de Laravel
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache