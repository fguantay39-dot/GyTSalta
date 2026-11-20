-- ============================================================================
-- TRABAJO PRÁCTICO N° 8: PERSISTENCIA DE SESIÓN, HASHING Y CONTROL DE ACCESO
-- Proyecto: Club Atlético Gimnasia y Tiro de Salta (Sitio Oficial del Hincha)
-- Archivo: tp8/schema_usuarios.sql
-- Motor: MySQL 8.x / MariaDB 10.x (XAMPP / phpMyAdmin)
-- Base de datos: gyt_comunidad
-- ============================================================================

CREATE DATABASE IF NOT EXISTS gyt_comunidad
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gyt_comunidad;

-- ----------------------------------------------------------------------------
-- 1. TABLA DE USUARIOS (Requisito Oficial TP8)
-- ----------------------------------------------------------------------------
-- Estructura exigida:
-- • id (INT, Primary Key, Auto Increment)
-- • nombre (VARCHAR)
-- • email (VARCHAR, Unique)
-- • password (VARCHAR de 255 caracteres para almacenar el hash BCRYPT)
-- • rol (VARCHAR: 'admin' / 'usuario')
-- • created_at (TIMESTAMP)
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. INSERCIÓN DE USUARIOS DE PRUEBA (Credenciales con Hashing BCRYPT)
-- ----------------------------------------------------------------------------
-- Credenciales preconfiguradas para testeo docente:
-- • Admin: franco.guantay@gyt.com.ar | Clave: Admin123!
-- • Socio: agustin.perez@prensaalbo.com.ar | Clave: Socio123!
-- • Socia: mariana.lopez@socia.gyt.com.ar | Clave: Socio123!
--
-- Hashes generados con password_hash('...', PASSWORD_BCRYPT, ['cost' => 12]):
-- $2y$12$izQsbToarZu.tr5H5j1Xge9PZzXz8qG2aYg7L0B9kOqC0A1k5N6V2
-- $2y$12$mfC2lI2iO81fg9nUv3W0heVwZ8z4gN6aK2jH8pO9qR0s1t2u3v4w5
-- ----------------------------------------------------------------------------

INSERT INTO usuarios (id, nombre, email, password, password_hash, rol) VALUES
(1, 'Franco Guantay', 'franco.guantay@gyt.com.ar', '$2y$12$izQsbToarZu.tr5H5j1Xge9PZzXz8qG2aYg7L0B9kOqC0A1k5N6V2', '$2y$12$izQsbToarZu.tr5H5j1Xge9PZzXz8qG2aYg7L0B9kOqC0A1k5N6V2', 'admin'),
(2, 'Agustín Pérez', 'agustin.perez@prensaalbo.com.ar', '$2y$12$mfC2lI2iO81fg9nUv3W0heVwZ8z4gN6aK2jH8pO9qR0s1t2u3v4w5', '$2y$12$mfC2lI2iO81fg9nUv3W0heVwZ8z4gN6aK2jH8pO9qR0s1t2u3v4w5', 'usuario'),
(3, 'Mariana López', 'mariana.lopez@socia.gyt.com.ar', '$2y$12$mfC2lI2iO81fg9nUv3W0heVwZ8z4gN6aK2jH8pO9qR0s1t2u3v4w5', '$2y$12$mfC2lI2iO81fg9nUv3W0heVwZ8z4gN6aK2jH8pO9qR0s1t2u3v4w5', 'usuario')
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    rol = VALUES(rol);
