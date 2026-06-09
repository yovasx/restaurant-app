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
mkdir -p storage/framework/views storage/framework/cache || true
mkdir -p /tmp/laravel-excel || true
mkdir -p storage/app/backups/database || true

# Crear fichero de log si no existe
touch storage/logs/laravel.log || true

# Soporte para UID/GID del host (evita problemas de permisos cuando montas código desde host)
HOST_UID=${HOST_UID:-}
HOST_GID=${HOST_GID:-}
if [ -n "$HOST_UID" ]; then
  echo "[entrypoint] ajustando propietario a $HOST_UID:${HOST_GID:-$HOST_UID}"
  chown -R "$HOST_UID":"${HOST_GID:-$HOST_UID}" storage bootstrap/cache || true
elif id www-data >/dev/null 2>&1; then
  chown -R www-data:www-data storage bootstrap/cache || true
fi

chmod -R 777 storage bootstrap/cache || true
chmod 666 storage/logs/laravel.log || true

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

# Comprobar y generar .env y APP_KEY si es necesario
if [ ! -f .env ] && [ -f .env.example ]; then
  echo "[entrypoint] Creando archivo .env a partir de .env.example"
  cp .env.example .env || true
fi

if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
  echo "[entrypoint] Generando APP_KEY..."
  php artisan key:generate || true
fi

# Limpiar caches para evitar errores con cambios en config
if command -v php >/dev/null 2>&1; then
  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
fi

# Ejecutar migraciones si se solicita
MIGRATE_ON_START=${MIGRATE_ON_START:-0}
if [ "$MIGRATE_ON_START" = "1" ]; then
  echo "[entrypoint] ejecutando migraciones: php artisan migrate --force"
  php artisan migrate --force || true
fi

echo "[entrypoint] listo — ejecutando: $@"

exec "$@"
