-- ============================================
-- MÓDULO: CONTROL DE ACCESO
-- ============================================

CREATE TABLE modulos (
    id         BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,     -- administracion_restaurante | administracion | perfil
    tipo       VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE roles (
    id         BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(50) NOT NULL,      -- restaurante | comensal | administrador
    estado     VARCHAR(20) DEFAULT 'activo',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE operaciones (
    id         BIGSERIAL PRIMARY KEY,
    modulo_id  BIGINT REFERENCES modulos(id) ON DELETE CASCADE,
    nombre     VARCHAR(100),
    leer       BOOLEAN DEFAULT false,
    crear      BOOLEAN DEFAULT false,
    editar     BOOLEAN DEFAULT false,
    eliminar   BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE rol_operacion (
    id           BIGSERIAL PRIMARY KEY,
    rol_id       BIGINT REFERENCES roles(id) ON DELETE CASCADE,
    operacion_id BIGINT REFERENCES operaciones(id) ON DELETE CASCADE,
    UNIQUE(rol_id, operacion_id)
);

CREATE TABLE usuarios (
    id          BIGSERIAL PRIMARY KEY,
    rol_id      BIGINT REFERENCES roles(id) ON DELETE SET NULL,
    nombre      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    contrasena  VARCHAR(255) NOT NULL,
    telefono    VARCHAR(20),
    estado      VARCHAR(20) DEFAULT 'activo',
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);

-- ============================================
-- MÓDULO: RESTAURANTES Y COMENSALES
-- ============================================

CREATE TABLE restaurantes (
    id               BIGSERIAL PRIMARY KEY,
    nombre           VARCHAR(150) NOT NULL,
    descripcion      TEXT,
    direccion        VARCHAR(255),
    latitud          DECIMAL(10, 8),
    longitud         DECIMAL(11, 8),
    horario_apertura TIME,
    horario_cierre   TIME,
    foto_portada     VARCHAR(255),
    telefono         VARCHAR(20),
    fecha_registro   DATE,
    estado           VARCHAR(20) DEFAULT 'activo',
    created_at       TIMESTAMP,
    updated_at       TIMESTAMP
);

CREATE TABLE comensales (
    id         BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono   VARCHAR(20),
    estado     VARCHAR(20) DEFAULT 'activo',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- ============================================
-- MÓDULO: CATEGORÍAS Y MENÚ
-- ============================================

CREATE TABLE categorias (
    id               BIGSERIAL PRIMARY KEY,
    modulo_id        BIGINT REFERENCES modulos(id) ON DELETE SET NULL,
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion      TEXT,
    estado           VARCHAR(20) DEFAULT 'activo',
    created_at       TIMESTAMP,
    updated_at       TIMESTAMP
);

-- Pivot: restaurante pertenece a categorias
CREATE TABLE restaurante_categorias (
    restaurante_id BIGINT REFERENCES restaurantes(id) ON DELETE CASCADE,
    categoria_id   BIGINT REFERENCES categorias(id) ON DELETE CASCADE,
    PRIMARY KEY (restaurante_id, categoria_id)
);

CREATE TABLE menus (
    id             BIGSERIAL PRIMARY KEY,
    restaurante_id BIGINT REFERENCES restaurantes(id) ON DELETE CASCADE,
    categoria_id   BIGINT REFERENCES categorias(id) ON DELETE SET NULL,
    nombre         VARCHAR(150) NOT NULL,
    descripcion    TEXT,
    precio         DECIMAL(10, 2) NOT NULL,
    foto_plato     VARCHAR(255),
    tipo           VARCHAR(20) DEFAULT 'fijo',      -- fijo | del_dia
    estado         VARCHAR(20) DEFAULT 'activo',
    orden          INT DEFAULT 0,
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);

CREATE TABLE disponibilidades (
    id         BIGSERIAL PRIMARY KEY,
    menu_id    BIGINT REFERENCES menus(id) ON DELETE CASCADE,
    fecha      DATE NOT NULL,
    estado     VARCHAR(20) DEFAULT 'disponible',    -- disponible | agotado
    cantidad   INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- ============================================
-- MÓDULO: INTERACCIÓN COMENSAL
-- ============================================

CREATE TABLE favoritos (
    id             BIGSERIAL PRIMARY KEY,
    restaurante_id BIGINT REFERENCES restaurantes(id) ON DELETE CASCADE,
    comensal_id    BIGINT REFERENCES comensales(id) ON DELETE CASCADE,
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP,
    UNIQUE(restaurante_id, comensal_id)
);

CREATE TABLE visitas (
    id             BIGSERIAL PRIMARY KEY,
    restaurante_id BIGINT REFERENCES restaurantes(id) ON DELETE CASCADE,
    comensal_id    BIGINT REFERENCES comensales(id) ON DELETE CASCADE,
    fecha_visita   DATE NOT NULL,
    metodo         VARCHAR(50) DEFAULT 'geolocalizacion',
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);

CREATE TABLE resenas (
    id                    BIGSERIAL PRIMARY KEY,
    comensal_id           BIGINT REFERENCES comensales(id) ON DELETE CASCADE,
    menu_id               BIGINT REFERENCES menus(id) ON DELETE CASCADE,
    visita_id             BIGINT REFERENCES visitas(id) ON DELETE SET NULL,
    score                 INT CHECK (score BETWEEN 1 AND 5),
    comentario            TEXT,
    respuesta_restaurante TEXT,
    created_at            TIMESTAMP,
    updated_at            TIMESTAMP
);

-- ============================================
-- MÓDULO: PROMOCIONES Y NOTIFICACIONES
-- ============================================

CREATE TABLE promociones (
    id             BIGSERIAL PRIMARY KEY,
    restaurante_id BIGINT REFERENCES restaurantes(id) ON DELETE CASCADE,
    nombre         VARCHAR(150) NOT NULL,
    tipo           VARCHAR(30) DEFAULT 'descuento',  -- descuento | 2x1 | postre
    valor          DECIMAL(10, 2),
    condicion      TEXT,
    fecha_inicio   DATE,
    fecha_fin      DATE,
    publicidad     VARCHAR(20) DEFAULT 'imagen',     -- imagen | video
    estado         VARCHAR(20) DEFAULT 'activo',
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);

CREATE TABLE notificaciones (
    id           BIGSERIAL PRIMARY KEY,
    comensal_id  BIGINT REFERENCES comensales(id) ON DELETE CASCADE,
    promocion_id BIGINT REFERENCES promociones(id) ON DELETE SET NULL,
    tipo         VARCHAR(50),
    mensaje      TEXT,
    fecha_envio  DATE,
    estado       VARCHAR(20) DEFAULT 'no_leida',    -- leida | no_leida
    created_at   TIMESTAMP,
    updated_at   TIMESTAMP
);