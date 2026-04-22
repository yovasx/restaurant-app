#!/usr/bin/env bash
set -euo pipefail

# Deploy helper for Laravel projects
# Usage:
#   sudo ./deploy.sh
# Or set environment variables and run as deploy user:
#   WEBUSER=www-data WEBGROUP=www-data SKIP_MIGRATE=1 ./deploy.sh

WEBUSER="${WEBUSER:-www-data}"
WEBGROUP="${WEBGROUP:-www-data}"
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKIP_MIGRATE="${SKIP_MIGRATE:-1}"
RESTART_SERVICES="${RESTART_SERVICES:-0}"
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.1-fpm}"
WEB_SERVER_SERVICE="${WEB_SERVER_SERVICE:-nginx}"

echo "Proyecto: $PROJECT_DIR"
echo "Usuario web: $WEBUSER:$WEBGROUP"
echo "Skip migrate: $SKIP_MIGRATE"

if command -v sudo >/dev/null 2>&1; then
  SUDO="sudo"
else
  SUDO=""
fi

# Fix ownership and permissions
echo "Ajustando propietario y permisos en storage y bootstrap/cache..."
$SUDO chown -R "$WEBUSER":"$WEBGROUP" "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache" || true
$SUDO chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache" || true

# Composer
if command -v composer >/dev/null 2>&1; then
  echo "Instalando dependencias de Composer..."
  composer install --no-dev --optimize-autoloader --no-interaction
  composer dump-autoload --optimize
else
  echo "ERROR: composer no encontrado. Instala Composer y vuelve a intentar."
  exit 1
fi

# Laravel caches
echo "Limpiando y reconstruyendo cachés de Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear || true
php artisan optimize --force || true

if [ "$SKIP_MIGRATE" != "1" ]; then
  echo "Ejecutando migraciones (force)..."
  php artisan migrate --force
fi

if [ "$RESTART_SERVICES" = "1" ]; then
  echo "Reiniciando servicios ($PHP_FPM_SERVICE, $WEB_SERVER_SERVICE)..."
  $SUDO systemctl restart "$PHP_FPM_SERVICE" || true
  $SUDO systemctl restart "$WEB_SERVER_SERVICE" || true
fi

echo "Despliegue completado. Si sigue habiendo problemas con permisos, ejecuta:"
echo "  sudo chown -R $WEBUSER:$WEBGROUP storage bootstrap/cache"
echo "y revisa que el usuario del proceso web (ej. www-data) tenga permisos de escritura."
