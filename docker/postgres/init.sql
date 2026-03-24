-- =============================================================
--  PostgreSQL — Script de inicialización
--  Se ejecuta automáticamente la PRIMERA VEZ que se crea el contenedor
--  Archivo: docker/postgres/init.sql
-- =============================================================

-- Habilitar extensiones útiles para Laravel
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";   -- Genera UUIDs (uuid_generate_v4())
CREATE EXTENSION IF NOT EXISTS "pg_trgm";     -- Búsquedas de texto similares
CREATE EXTENSION IF NOT EXISTS "citext";      -- Texto case-insensitive

-- Configurar zona horaria del servidor PostgreSQL
SET timezone = 'America/La_Paz';
ALTER DATABASE laravel_db SET timezone TO 'America/La_Paz';

-- Mensaje de confirmación en los logs
DO $$
BEGIN
    RAISE NOTICE 'Base de datos laravel_db inicializada correctamente con extensiones UUID, pg_trgm y citext';
END
$$;
