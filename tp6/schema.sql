-- ============================================================================
-- TRABAJO PRÁCTICO N° 6: SCRIPT DDL DE CREACIÓN DE ESQUEMA EN MYSQL
-- Proyecto: Gimnasia y Tiro de Salta (Sitio Oficial del Hincha y Comunidad)
-- Archivo: tp6/schema.sql
-- Motor: MySQL 8.x / MariaDB 10.x (XAMPP / Laragon / phpMyAdmin)
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. CREACIÓN DE LA BASE DE DATOS
-- ----------------------------------------------------------------------------
-- Nota: La base de datos del proyecto se denomina 'gyt_comunidad' para mantener
-- compatibilidad con la conexión PDO existente (db.php).
-- Si la cátedra requiere el nombre genérico 'mi_proyecto_web', basta con modificarlo aquí.
CREATE DATABASE IF NOT EXISTS gyt_comunidad
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gyt_comunidad;

-- Desactivar temporalmente chequeos de claves foráneas para recreación segura si se requiere
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS publicacion_etiquetas;
DROP TABLE IF EXISTS etiquetas;
DROP TABLE IF EXISTS mensajes_contacto;
DROP TABLE IF EXISTS publicaciones;
DROP TABLE IF EXISTS estados;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 2. TABLAS MAESTRAS / SECUNDARIAS
-- ----------------------------------------------------------------------------

-- 2.1. Tabla de Roles de Usuario
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2. Tabla de Categorías de Contenido (Noticias, Secciones)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    slug VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3. Tabla de Estados de Publicación (Borrador, Publicado, Archivado)
CREATE TABLE estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(40) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.4. Tabla de Etiquetas (Taxonomía y palabras clave)
CREATE TABLE etiquetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(60) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. TABLA DE USUARIOS (Autenticación, roles y datos personales)
-- ----------------------------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(30) NULL,
    dni VARCHAR(20) NULL UNIQUE,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. TABLA PRINCIPAL DE NEGOCIO: PUBLICACIONES / NOTICIAS
-- ----------------------------------------------------------------------------
CREATE TABLE publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    estado_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    bajada VARCHAR(300) NULL,
    contenido TEXT NOT NULL,
    imagen_url VARCHAR(500) NULL,
    visitas INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_publicaciones_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_publicaciones_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_publicaciones_estado
        FOREIGN KEY (estado_id) REFERENCES estados (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. TABLA DE RELACIÓN MUCHOS A MUCHOS (N:M): PUBLICACION_ETIQUETAS
-- ----------------------------------------------------------------------------
CREATE TABLE publicacion_etiquetas (
    publicacion_id INT NOT NULL,
    etiqueta_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (publicacion_id, etiqueta_id),

    CONSTRAINT fk_pe_publicacion
        FOREIGN KEY (publicacion_id) REFERENCES publicaciones (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_pe_etiqueta
        FOREIGN KEY (etiqueta_id) REFERENCES etiquetas (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. TABLA ADICIONAL: MENSAJES DE CONTACTO (Formulario Web Clase 5)
-- ----------------------------------------------------------------------------
CREATE TABLE mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. OPTIMIZACIÓN CON ÍNDICES EXPLÍCITOS (CREATE INDEX)
-- ----------------------------------------------------------------------------
-- Índice 1: Búsqueda rápida y autenticación por correo electrónico
CREATE INDEX idx_usuarios_email ON usuarios (email);

-- Índice 2: Filtrado frecuente de publicaciones por categoría
CREATE INDEX idx_publicaciones_categoria ON publicaciones (categoria_id);

-- Índice 3: Filtrado combinado de publicaciones por estado y fecha de publicación
CREATE INDEX idx_publicaciones_estado_fecha ON publicaciones (estado_id, created_at DESC);

-- Índice 4: Búsqueda por slug amigable para URLs del portal web
CREATE INDEX idx_publicaciones_slug ON publicaciones (slug);

-- Índice 5: Filtrado de mensajes de contacto no leídos
CREATE INDEX idx_mensajes_leido ON mensajes_contacto (leido, created_at DESC);
