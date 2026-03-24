# 🐳 Stack Docker para Laravel — Guía Completa

## 📦 Tecnologías incluidas

| Servicio    | Imagen                  | Puerto local     |
|-------------|-------------------------|------------------|
| PHP-FPM     | `php:8.3-fpm`           | Interno (9000)   |
| Nginx       | `nginx:1.27-alpine`     | `localhost:8080` |
| PostgreSQL  | `postgres:17.2`         | `localhost:5432` |
| pgAdmin 4   | `dpage/pgadmin4:8.14`   | `localhost:5050` |
| Redis       | `redis:7.4-alpine`      | `localhost:6379` |

---

## 🗂 Estructura de carpetas

```
proyecto-docker/
├── docker-compose.yml          ← Orquestación de servicios
├── Dockerfile                  ← Imagen PHP personalizada para Laravel
├── Makefile                    ← Comandos de conveniencia
├── .env.example                ← Variables de entorno (copia como .env)
├── .gitignore
├── docker/
│   ├── nginx/
│   │   └── default.conf        ← Configuración Nginx para Laravel
│   ├── php/
│   │   ├── php.ini             ← Configuración PHP
│   │   └── php-fpm.conf        ← Configuración PHP-FPM
│   ├── postgres/
│   │   └── init.sql            ← Script SQL inicial (extensiones)
│   └── pgadmin/
│       └── servers.json        ← pgAdmin preconfigurado
└── src/                        ← ← ← TU PROYECTO LARAVEL VA AQUÍ
```

---

## 🚀 Primer uso — Setup completo

### Opción A: Con Make (recomendado)
```bash
#crea una carpeta donde ira el proyecto y entra 
#en la consola (powershell/git bash) escribe:
git clone https://github.com/yovasx/restaurant-app.git

# Copia el archivo de entorno
cp .env.example .env

# Pon tu proyecto Laravel en la carpeta src/
# (o créalo nuevo, ver más abajo)

# Ejecuta el setup completo con un solo comando:
make setup
```

### Opción B: Manual paso a paso
```bash
# 1. Copia el .env
cp .env.example .env

# 2. Construye e inicia los contenedores
docker compose up -d --build

# 3. Espera ~15 segundos que Postgres arranque
sleep 15

# 4. Instala dependencias de Composer
docker compose exec app composer install

# 5. Genera la APP_KEY (CRÍTICO — sin esto Laravel da error 500)
docker compose exec app php artisan key:generate

# 6. Crea las tablas de la base de datos
docker compose exec app php artisan migrate

# 7. Crea el enlace simbólico de storage
docker compose exec app php artisan storage:link
```

---

## 🆕 Crear un proyecto Laravel nuevo desde cero

Si aún no tienes un proyecto Laravel:

```bash
# 1. Crea la carpeta src/
mkdir src

# 2. Levanta los contenedores (solo app para usar Composer)
docker compose up -d --build

# 3. Crea el proyecto Laravel dentro del contenedor
docker compose exec app composer create-project laravel/laravel . --prefer-dist

# 4. Genera la key
docker compose exec app php artisan key:generate

# 5. Migra la base de datos
docker compose exec app php artisan migrate
```

---

## 🔑 Configuración del .env de Laravel

**El error más común** es tener `DB_HOST=localhost` en el `.env` de Laravel.  
Dentro de Docker, `localhost` se refiere al contenedor mismo, no a PostgreSQL.  
**Usa el nombre del servicio:**

```env
# ❌ INCORRECTO — no funciona dentro de Docker
DB_HOST=localhost

# ✅ CORRECTO — nombre del servicio en docker-compose.yml
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=secret

# ✅ CORRECTO — Redis igual
REDIS_HOST=redis
```

---

## 🌐 URLs de acceso

| Servicio      | URL                                          |
|---------------|----------------------------------------------|
| **Laravel**   | http://localhost:8080                        |
| **pgAdmin**   | http://localhost:5050                        |
| **pgAdmin login** | `admin@admin.com` / `admin123`          |

---

## 🔧 Comandos del día a día

```bash
# ── Servicios ──
make up             # Levantar todo
make down           # Detener todo
make restart        # Reiniciar
make ps             # Ver estado
make logs           # Ver logs en vivo

# ── Laravel ──
make artisan CMD="migrate --seed"
make artisan CMD="make:model Producto -mcr"
make composer CMD="require spatie/laravel-permission"
make bash           # Shell dentro del contenedor
make tinker         # Laravel Tinker

# ── Base de datos ──
make migrate
make migrate-fresh  # ⚠ Borra y recrea todo
make psql           # Consola de PostgreSQL

# ── Caché ──
make cache-clear    # Limpia todos los cachés

# ── Permisos ──
make permissions    # Arregla permisos de storage
```

---

## ❗ Solución de errores comunes

### Error: "No application encryption key has been specified"
```bash
docker compose exec app php artisan key:generate
```

### Error: "could not translate host name postgres to address"
- Verifica que `DB_HOST=postgres` en tu `.env` (nombre del servicio, NO localhost)
- Verifica que el contenedor de Postgres esté corriendo: `make ps`

### Error: "Permission denied" en storage/
```bash
make permissions
```

### Error: "Class not found" o autoload roto
```bash
docker compose exec app composer dump-autoload
```

### Error 502 Bad Gateway en Nginx
- PHP-FPM no está listo. Espera unos segundos y recarga.
- Revisa los logs: `make logs-app`

### Error al conectar en pgAdmin
- El servidor ya viene preconfigurado. Si pide contraseña, usa `secret`
- Verifica que el nombre del servidor en pgAdmin sea `postgres` (no localhost)

---

## 🗑 Limpiar todo y empezar de cero

```bash
# Elimina contenedores Y volúmenes (borra la base de datos también)
make fresh

# Luego vuelve a hacer setup
make setup
```

---

## 🔒 Para producción

Antes de ir a producción, actualiza el `.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`  
- Cambia todas las contraseñas
- Activa `opcache.enable=1` en `docker/php/php.ini`
- Elimina la exposición del puerto 5432 en `docker-compose.yml`
