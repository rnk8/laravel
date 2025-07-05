#!/bin/bash
set -e

# Ejecuta migraciones
php artisan migrate --force

# Inicia Apache
exec apache2-foreground