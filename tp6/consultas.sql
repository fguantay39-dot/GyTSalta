-- ============================================================================
-- TRABAJO PRÁCTICO N° 6: CONSULTAS DE PRUEBA CRUD (DML)
-- Proyecto: Gimnasia y Tiro de Salta (Sitio Oficial del Hincha y Comunidad)
-- Archivo: tp6/consultas.sql
-- Motor: MySQL 8.x / MariaDB 10.x (XAMPP / Laragon / phpMyAdmin)
-- ============================================================================

USE gyt_comunidad;

-- ============================================================================
-- 1. CREATE (Inserción de Datos Iniciales de Prueba)
-- ============================================================================

-- 1.1. Inserciones en Tablas Maestras / Secundarias

-- Inserción de Roles (mínimo 2 exigidos, cargamos 3 representativos)
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Control total del sistema, gestión de usuarios y contenidos'),
('Redactor', 'Carga, edición y redacción de noticias del club'),
('Socio', 'Acceso a beneficios exclusivos de la comunidad y comentarios');

-- Inserción de Categorías (mínimo 2 exigidas, cargamos 4 representativas)
INSERT INTO categorias (nombre, slug, descripcion) VALUES
('Fútbol Profesional', 'futbol-profesional', 'Cobertura de la Primera Nacional, partidos y plantel'),
('Club e Institucional', 'club-e-institucional', 'Novedades de la comisión directiva, obras y disciplinas'),
('Historia y Cultura', 'historia-y-cultura', 'Hitos, glorias y efemérides de Gimnasia y Tiro de Salta'),
('El Gigante del Norte', 'el-gigante-del-norte', 'Accesos, remodelaciones y venta de entradas al estadio');

-- Inserción de Estados
INSERT INTO estados (nombre, descripcion) VALUES
('Borrador', 'Artículo en proceso de redacción, no visible al público'),
('Publicado', 'Artículo activo y visible en el sitio web'),
('Archivado', 'Artículo histórico no listado en la portada principal');

-- Inserción de Etiquetas
INSERT INTO etiquetas (nombre, slug) VALUES
('Primera Nacional', 'primera-nacional'),
('Gigante del Norte', 'gigante-del-norte'),
('Invicto', 'invicto'),
('Plantel Profesional', 'plantel-profesional'),
('Clásico', 'clasico');

-- Inserción de Usuarios de Prueba (con hashes compatibles con password_hash de PHP)
INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono, dni) VALUES
(1, 'Franco', 'Guantay', 'franco.guantay@gyt.com.ar', '$2y$10$e7V7eR71bQe2lR1QG4gKveYq8d8U8jX9f0L3yM8p2k5J7b3V1a0Wq', '3874123456', '41234567'),
(2, 'Agustín', 'Pérez', 'agustin.perez@prensaalbo.com.ar', '$2y$10$e7V7eR71bQe2lR1QG4gKveYq8d8U8jX9f0L3yM8p2k5J7b3V1a0Wq', '3875987654', '39876543'),
(3, 'Mariana', 'López', 'mariana.lopez@socia.gyt.com.ar', '$2y$10$e7V7eR71bQe2lR1QG4gKveYq8d8U8jX9f0L3yM8p2k5J7b3V1a0Wq', '3876112233', '38112233');

-- 1.2. Inserciones en la Tabla Principal (Publicaciones vinculadas a FKs)
-- (mínimo 2 inserciones exigidas por la consigna; agregamos 3 notas reales del portal)
INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, slug, bajada, contenido, imagen_url, visitas) VALUES
(
    1,
    1,
    2,
    'Gimnasia y Tiro se prepara para su próximo partido',
    'gimnasia-y-tiro-se-prepara-para-su-proximo-partido',
    'El plantel entrena en Salta con la mirada puesta en el próximo compromiso de la Primera Nacional.',
    'El plantel profesional del Club Atlético Gimnasia y Tiro completó esta mañana una nueva sesión de entrenamiento táctico bajo las órdenes del cuerpo técnico en el predio de Limache. Se evaluaron alternativas para el once titular de cara al encuentro del próximo domingo.',
    'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80',
    340
),
(
    2,
    4,
    2,
    'El Gigante del Norte espera a su gente en una nueva fecha',
    'el-gigante-del-norte-espera-a-su-gente',
    'Los hinchas acompañarán al Albo en una nueva jornada como local con venta anticipada de entradas.',
    'Se dieron a conocer los días y horarios de apertura de boleterías para el partido de este fin de semana en el estadio El Gigante del Norte. Se recuerda a los socios con cuota al día que podrán ingresar con su carnet digital habilitado.',
    'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=1200&q=80',
    520
),
(
    1,
    3,
    2,
    'Una historia que representa a Salta: Camino al aniversario',
    'una-historia-que-representa-a-salta',
    'Repasamos los momentos históricos más destacados de la institución decana del deporte salteño.',
    'Fundado el 29 de noviembre de 1902, Gimnasia y Tiro ha sido testigo y protagonista del desarrollo deportivo y social de nuestra provincia. En este informe especial repasamos las décadas doradas y los ascensos que marcaron a generaciones enteras.',
    'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1200&q=80',
    180
),
(
    2,
    1,
    1,
    'Borrador táctico: Análisis de pelota parada',
    'borrador-tactico-analisis-pelota-parada',
    'Notas internas sobre el rendimiento en balones detenidos.',
    'Contenido preliminar en revisión por el departamento de prensa deportiva.',
    NULL,
    0
);

-- Inserciones en la Tabla de Relación N:M (publicacion_etiquetas)
INSERT INTO publicacion_etiquetas (publicacion_id, etiqueta_id) VALUES
(1, 1), -- Noticia 1 -> Primera Nacional
(1, 4), -- Noticia 1 -> Plantel Profesional
(2, 2), -- Noticia 2 -> Gigante del Norte
(2, 1), -- Noticia 2 -> Primera Nacional
(3, 2); -- Noticia 3 -> Gigante del Norte

-- Inserción en Mensajes de Contacto (formulario Clase 5)
INSERT INTO mensajes_contacto (nombre, email, mensaje, leido) VALUES
('Carlos Benítez', 'carlos.benitez@gmail.com', 'Buenas tardes, quisiera consultar los requisitos para asociar a mi hijo al club.', 0),
('Valeria Morales', 'valeria.m@hotmail.com', 'Excelente cobertura del último partido. ¡Aguante el Albo!', 1),
('Mensaje de Prueba Borrable', 'test@eliminar.com', 'Este mensaje fue creado para probar la consulta DELETE.', 0);

-- ============================================================================
-- 2. READ (Consultas y Uniones)
-- ============================================================================

-- 2.1. Consulta SELECT con cláusula WHERE para filtrar y ORDER BY para ordenar
-- Objetivo: Obtener todas las publicaciones activas (estado 'Publicado' = 2) de la categoría 'Fútbol Profesional' (categoria_id = 1),
-- ordenadas de la más reciente a la más antigua.
SELECT 
    id,
    titulo,
    slug,
    bajada,
    visitas,
    created_at
FROM publicaciones
WHERE estado_id = 2 
  AND categoria_id = 1
ORDER BY created_at DESC;

-- 2.2. Consulta SELECT utilizando INNER JOIN entre múltiples tablas del sistema
-- Objetivo: Listar los datos de las publicaciones uniendo la tabla principal con la tabla de Usuarios (autor),
-- la tabla de Categorías y la tabla de Estados, mostrando la información normalizada de forma legible.
SELECT 
    p.id AS publicacion_id,
    p.titulo,
    CONCAT(u.nombre, ' ', u.apellido) AS autor_nombre,
    u.email AS autor_email,
    r.nombre AS autor_rol,
    c.nombre AS categoria,
    e.nombre AS estado,
    p.visitas,
    p.created_at AS fecha_publicacion
FROM publicaciones p
INNER JOIN usuarios u ON p.usuario_id = u.id
INNER JOIN roles r ON u.rol_id = r.id
INNER JOIN categorias c ON p.categoria_id = c.id
INNER JOIN estados e ON p.estado_id = e.id
WHERE e.nombre = 'Publicado'
ORDER BY p.created_at DESC;

-- 2.3. Consulta SELECT avanzada con relación N:M (INNER JOIN + LEFT JOIN + GROUP_CONCAT)
-- Objetivo: Traer cada publicación junto con todas sus etiquetas asociadas agrupadas en un solo resultado.
SELECT 
    p.id,
    p.titulo,
    c.nombre AS categoria,
    GROUP_CONCAT(t.nombre SEPARATOR ', ') AS etiquetas_asociadas
FROM publicaciones p
INNER JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN publicacion_etiquetas pe ON p.id = pe.publicacion_id
LEFT JOIN etiquetas t ON pe.etiqueta_id = t.id
GROUP BY p.id, p.titulo, c.nombre
ORDER BY p.id ASC;

-- ============================================================================
-- 3. UPDATE (Actualización de Registros con Cláusula WHERE Estricta)
-- ============================================================================

-- Modificación 1: Actualizar el estado de una publicación de 'Borrador' a 'Publicado'
-- y asignar su bajada final mediante su clave primaria id = 4.
UPDATE publicaciones
SET 
    estado_id = 2,
    bajada = 'Informe completado: El análisis táctico arrojó mejoras sustanciales en la marca zonal.',
    updated_at = CURRENT_TIMESTAMP
WHERE id = 4;

-- Modificación 2: Incrementar el contador de visitas de una publicación específica (id = 1)
UPDATE publicaciones
SET 
    visitas = visitas + 1,
    updated_at = CURRENT_TIMESTAMP
WHERE id = 1;

-- Modificación 3: Marcar como leído un mensaje de contacto específico recibido en el sitio
UPDATE mensajes_contacto
SET 
    leido = 1,
    updated_at = CURRENT_TIMESTAMP
WHERE id = 1;

-- ============================================================================
-- 4. DELETE (Eliminación de Registros con Cláusula WHERE Estricta)
-- ============================================================================

-- Eliminación 1: Dar de baja un registro de prueba en la tabla mensajes_contacto
-- utilizando estrictamente la clave primaria id para garantizar que sólo se elimine ese registro.
DELETE FROM mensajes_contacto
WHERE id = 3;

-- Eliminación 2: Eliminar una relación de etiqueta de prueba en una publicación
-- asegurando el borrado selectivo en la tabla de asociación N:M
DELETE FROM publicacion_etiquetas
WHERE publicacion_id = 3 AND etiqueta_id = 2;
