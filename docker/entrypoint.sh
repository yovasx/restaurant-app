#!/usr/bin/env bash
set -euo pipefail

# Entrypoint para el contenedor PHP-FPM
# - Ajusta permisos de storage y bootstrap/cache
# - Crea el archivo de log si falta
# - Ejecuta composer install si falta vendor
# - Limpia cachés básicos de Laravel

cd /var/www/html || exit 0

echo "[entrypoint] arrancando en $(pwd)"

# Asegurar carpetas existan
mkdir -p storage/logs bootstrap/cache vendor || true

# Crear fichero de log si no existe
touch storage/logs/laravel.log || true

# Intentar ajustar propietario/permiso (no fallar si no permitido)
if id www-data >/dev/null 2>&1; then
  chown -R www-data:www-data storage bootstrap/cache || true
fi
chmod -R 775 storage bootstrap/cache || true
chmod 664 storage/logs/laravel.log || true || true

# Instala dependencias composer si no existe autoload
if [ ! -f vendor/autoload.php ]; then
  echo "[entrypoint] vendor/autoload.php no encontrado — ejecutando composer install"
  if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction || true
  else
    echo "[entrypoint] WARNING: composer no está instalado en el contenedor"
  fi
fi

# Siempre regenerar autoload (seguro y rápido si ya existe)
if command -v composer >/dev/null 2>&1; then
  composer dump-autoload --optimize --no-interaction || true
fi

# Limpiar caches para evitar errores con cambios en config
if command -v php >/dev/null 2>&1; then
  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
fi

echo "[entrypoint] listo — ejecutando: $@"

exec "$@"
