## =============================================================
##  Makefile — Comandos de conveniencia para el equipo
##  Uso: make <comando>
##  Ej:  make up | make artisan CMD="migrate" | make composer CMD="install"
## =============================================================

.DEFAULT_GOAL := help
COMPOSE = docker compose

## ── Iniciar / Detener ─────────────────────────────────────────

up: ## Levantar todos los servicios en segundo plano
	$(COMPOSE) up -d --build

down: ## Detener y eliminar contenedores (datos persistidos en volúmenes)
	$(COMPOSE) down

restart: ## Reiniciar todos los servicios
	$(COMPOSE) restart

stop: ## Parar los contenedores sin eliminarlos
	$(COMPOSE) stop

fresh: ## ⚠ Eliminar TODO (contenedores + volúmenes). Empieza de cero.
	$(COMPOSE) down -v --remove-orphans

## ── Estado y Logs ─────────────────────────────────────────────

ps: ## Ver estado de todos los contenedores
	$(COMPOSE) ps

logs: ## Ver logs de todos los servicios (últimas 100 líneas)
	$(COMPOSE) logs --tail=100 -f

logs-app: ## Ver logs solo de la app PHP
	$(COMPOSE) logs --tail=100 -f app

logs-nginx: ## Ver logs de Nginx
	$(COMPOSE) logs --tail=100 -f nginx

logs-postgres: ## Ver logs de PostgreSQL
	$(COMPOSE) logs --tail=100 -f postgres

## ── Laravel / PHP ─────────────────────────────────────────────

artisan: ## Correr comando artisan. Ej: make artisan CMD="migrate --seed"
	$(COMPOSE) exec app php artisan $(CMD)

composer: ## Correr comando composer. Ej: make composer CMD="install"
	$(COMPOSE) exec app composer $(CMD)

bash: ## Abrir shell interactivo en el contenedor app
	$(COMPOSE) exec app bash

tinker: ## Abrir Laravel Tinker (REPL de Laravel)
	$(COMPOSE) exec app php artisan tinker

## ── Base de datos ─────────────────────────────────────────────

migrate: ## Correr migraciones de Laravel
	$(COMPOSE) exec app php artisan migrate

migrate-fresh: ## ⚠ Eliminar TODAS las tablas y volver a migrar
	$(COMPOSE) exec app php artisan migrate:fresh --seed

psql: ## Abrir consola interactiva de PostgreSQL
	$(COMPOSE) exec postgres psql -U laravel_user -d laravel_db

## ── Setup inicial ─────────────────────────────────────────────

setup: ## Primera vez: copia .env, instala deps, genera clave y migra
	@echo "📦 Copiando .env.example → .env"
	@cp -n .env.example .env || true
	@echo "📦 Levantando contenedores..."
	$(COMPOSE) up -d --build
	@echo "⏳ Esperando que los servicios estén listos..."
	@sleep 15
	@echo "📦 Instalando dependencias de Composer..."
	$(COMPOSE) exec app composer install --no-interaction
	@echo "🔑 Generando APP_KEY..."
	$(COMPOSE) exec app php artisan key:generate
	@echo "🗃 Ejecutando migraciones..."
	$(COMPOSE) exec app php artisan migrate
	@echo "🔗 Creando enlace de storage..."
	$(COMPOSE) exec app php artisan storage:link
	@echo ""
	@echo "✅ Setup completo."
	@echo "🌐 App:     http://localhost:8080"
	@echo "🐘 pgAdmin: http://localhost:5050"

## ── Caché Laravel ─────────────────────────────────────────────

cache-clear: ## Limpiar todos los cachés de Laravel
	$(COMPOSE) exec app php artisan optimize:clear
	$(COMPOSE) exec app php artisan cache:clear
	$(COMPOSE) exec app php artisan config:clear
	$(COMPOSE) exec app php artisan route:clear
	$(COMPOSE) exec app php artisan view:clear

## ── Permisos ──────────────────────────────────────────────────

permissions: ## Arreglar permisos de storage y bootstrap/cache
	$(COMPOSE) exec app chmod -R 775 storage bootstrap/cache
	$(COMPOSE) exec app chown -R appuser:appgroup storage bootstrap/cache

## ── Ayuda ─────────────────────────────────────────────────────

help: ## Mostrar esta ayuda
	@echo ""
	@echo "  Stack Laravel + Docker — Comandos disponibles"
	@echo "  ─────────────────────────────────────────────"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""

.PHONY: up down restart stop fresh ps logs logs-app logs-nginx logs-postgres \
        artisan composer bash tinker migrate migrate-fresh psql setup \
        cache-clear permissions help
