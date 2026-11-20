<?php
/**
 * admin_panel.php
 * Panel de Administración y Control Total del Club (Solo Rol 'admin')
 * Trabajo Práctico N° 8 - Control de Acceso (RBAC) y Gestión CMS
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/auth_guard.php';

// Control estricto de acceso por rol: solo permitido a rol 'admin'
verificar_rol('admin');

require_once __DIR__ . '/db.php';

$mensaje_exito = '';
$mensaje_error = '';

// Helper para generar slug a partir del título
function generarSlug(string $texto): string {
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $texto = preg_replace('/[áàäâã]/u', 'a', $texto);
    $texto = preg_replace('/[éèëê]/u', 'e', $texto);
    $texto = preg_replace('/[íìïî]/u', 'i', $texto);
    $texto = preg_replace('/[óòöôõ]/u', 'o', $texto);
    $texto = preg_replace('/[úùüû]/u', 'u', $texto);
    $texto = preg_replace('/[ñ]/u', 'n', $texto);
    $texto = preg_replace('/[^a-z0-9\-]/', '-', $texto);
    $texto = preg_replace('/-+/', '-', $texto);
    return trim($texto, '-') . '-' . time();
}

// PROCESAMIENTO DE ACCIONES ADMINISTRATIVAS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // 1. CREAR NUEVA NOTICIA
    if ($accion === 'crear_noticia') {
        $titulo       = trim((string)($_POST['titulo'] ?? ''));
        $categoria_id = (int)($_POST['categoria_id'] ?? 1);
        $estado_id    = (int)($_POST['estado_id'] ?? 2); // 2 = Publicado
        $bajada       = trim((string)($_POST['bajada'] ?? ''));
        $contenido    = trim((string)($_POST['contenido'] ?? ''));
        $imagen_url   = trim((string)($_POST['imagen_url'] ?? ''));

        if ($titulo === '' || $contenido === '') {
            $mensaje_error = 'El título y el contenido son obligatorios para publicar una noticia.';
        } else {
            try {
                $slug = generarSlug($titulo);
                $stmt = $pdo->prepare('
                    INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, slug, bajada, contenido, imagen_url)
                    VALUES (:usuario_id, :categoria_id, :estado_id, :titulo, :slug, :bajada, :contenido, :imagen_url)
                ');
                $stmt->execute([
                    'usuario_id'   => (int)$_SESSION['usuario_id'],
                    'categoria_id' => $categoria_id,
                    'estado_id'    => $estado_id,
                    'titulo'       => $titulo,
                    'slug'         => $slug,
                    'bajada'       => $bajada,
                    'contenido'    => $contenido,
                    'imagen_url'   => !empty($imagen_url) ? $imagen_url : 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80',
                ]);
                $mensaje_exito = '¡Noticia creada y publicada exitosamente!';
            } catch (PDOException $e) {
                error_log('Error creando noticia: ' . $e->getMessage());
                $mensaje_error = 'Error al guardar la noticia: ' . $e->getMessage();
            }
        }
    }

    // 2. EDITAR NOTICIA EXISTENTE
    if ($accion === 'editar_noticia') {
        $id           = (int)($_POST['noticia_id'] ?? 0);
        $titulo       = trim((string)($_POST['titulo'] ?? ''));
        $categoria_id = (int)($_POST['categoria_id'] ?? 1);
        $estado_id    = (int)($_POST['estado_id'] ?? 2);
        $bajada       = trim((string)($_POST['bajada'] ?? ''));
        $contenido    = trim((string)($_POST['contenido'] ?? ''));
        $imagen_url   = trim((string)($_POST['imagen_url'] ?? ''));

        if ($id <= 0 || $titulo === '') {
            $mensaje_error = 'ID de noticia inválido o título vacío.';
        } else {
            try {
                $stmt = $pdo->prepare('
                    UPDATE publicaciones 
                    SET titulo = :titulo, categoria_id = :categoria_id, estado_id = :estado_id, 
                        bajada = :bajada, contenido = :contenido, imagen_url = :imagen_url, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ');
                $stmt->execute([
                    'id'           => $id,
                    'titulo'       => $titulo,
                    'categoria_id' => $categoria_id,
                    'estado_id'    => $estado_id,
                    'bajada'       => $bajada,
                    'contenido'    => $contenido,
                    'imagen_url'   => $imagen_url,
                ]);
                $mensaje_exito = '¡Noticia #' . $id . ' modificada correctamente!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al actualizar la noticia: ' . $e->getMessage();
            }
        }
    }

    // 3. ELIMINAR NOTICIA
    if ($accion === 'eliminar_noticia') {
        $id = (int)($_POST['noticia_id'] ?? 0);
        if ($id > 0) {
            try {
                // Eliminar posibles relaciones en publicacion_etiquetas
                $pdo->prepare('DELETE FROM publicacion_etiquetas WHERE publicacion_id = :id')->execute(['id' => $id]);
                // Eliminar publicacion
                $stmt = $pdo->prepare('DELETE FROM publicaciones WHERE id = :id');
                $stmt->execute(['id' => $id]);
                $mensaje_exito = 'Noticia #' . $id . ' eliminada de la base de datos.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar la noticia: ' . $e->getMessage();
            }
        }
    }

    // 4. CAMBIAR ROL DE USUARIO
    if ($accion === 'cambiar_rol') {
        $usuario_id = (int)($_POST['target_user_id'] ?? 0);
        $nuevo_rol  = strtolower(trim((string)($_POST['nuevo_rol'] ?? 'usuario')));

        if (!in_array($nuevo_rol, ['admin', 'usuario'], true)) {
            $mensaje_error = 'Rol no permitido.';
        } elseif ($usuario_id === (int)$_SESSION['usuario_id'] && $nuevo_rol !== 'admin') {
            $mensaje_error = 'Por seguridad, no podés quitarte tu propio rol de administrador.';
        } else {
            try {
                $rol_id_fk = ($nuevo_rol === 'admin') ? 1 : 3;
                $stmt = $pdo->prepare('UPDATE usuarios SET rol = :rol, rol_id = :rol_id WHERE id = :id');
                $stmt->execute(['rol' => $nuevo_rol, 'rol_id' => $rol_id_fk, 'id' => $usuario_id]);
                $mensaje_exito = 'Rol del usuario #' . $usuario_id . ' actualizado a: ' . strtoupper($nuevo_rol);
            } catch (PDOException $e) {
                $mensaje_error = 'Error al cambiar rol: ' . $e->getMessage();
            }
        }
    }

    // 5. ELIMINAR MENSAJE DE CONTACTO
    if ($accion === 'eliminar_mensaje') {
        $msg_id = (int)($_POST['mensaje_id'] ?? 0);
        if ($msg_id > 0) {
            try {
                $pdo->prepare('DELETE FROM mensajes_contacto WHERE id = :id')->execute(['id' => $msg_id]);
                $mensaje_exito = 'Mensaje #' . $msg_id . ' eliminado.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar mensaje: ' . $e->getMessage();
            }
        }
    }
}

// Consultas para mostrar en el panel
$totalUsuarios       = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalPublicaciones  = (int)$pdo->query('SELECT COUNT(*) FROM publicaciones')->fetchColumn();
$totalMensajes       = (int)$pdo->query('SELECT COUNT(*) FROM mensajes_contacto')->fetchColumn();

// Listado de usuarios
$usuariosRegistrados = $pdo->query('SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY id ASC')->fetchAll();

// Listado de noticias con nombre de categoría y estado
$noticias = $pdo->query('
    SELECT p.id, p.titulo, p.bajada, p.imagen_url, p.created_at, c.nombre as categoria, e.nombre as estado, p.estado_id, p.categoria_id, p.contenido
    FROM publicaciones p
    INNER JOIN categorias c ON p.categoria_id = c.id
    INNER JOIN estados e ON p.estado_id = e.id
    ORDER BY p.id DESC
')->fetchAll();

// Listado de categorías y estados para los selects
$categorias = $pdo->query('SELECT id, nombre FROM categorias')->fetchAll();
$estados    = $pdo->query('SELECT id, nombre FROM estados')->fetchAll();

// Mensajes de contacto recibidos
$mensajes = $pdo->query('SELECT id, nombre, email, mensaje, created_at FROM mensajes_contacto ORDER BY id DESC LIMIT 5')->fetchAll();

$titulo_pagina = 'Panel de Administración Albo | ' . $nombre_sitio;
$pagina_actual = 'admin_panel';

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f4f6f9; padding: 40px 15px; min-height: calc(100vh - 200px);">
    <div style="max-width: 1200px; margin: 0 auto;">

        <!-- Mensajes de Alerta -->
        <?php if (!empty($mensaje_exito)): ?>
            <div style="background: #e8f5e9; border-left: 5px solid #2e7d32; color: #1b5e20; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div style="background: #ffebee; border-left: 5px solid #d32f2f; color: #c62828; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Cabecera del Panel de Control -->
        <div style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: #e63946; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-lock"></i> Zona de Administración y Edición
                </span>
                <h1 style="margin: 10px 0 5px; font-size: 1.8rem;">Panel de Administración Albo</h1>
                <p style="margin: 0; opacity: 0.85; font-size: 0.95rem;">
                    Conectado como <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Franco Guantay'); ?></strong> (<code><?= htmlspecialchars($_SESSION['usuario_email'] ?? 'fguantay39@gmail.com'); ?></code>) — <em>Permisos totales de edición</em>
                </p>
            </div>

            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="noticias.php" style="background: #0077b6; color: #fff; padding: 9px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.88rem;">
                    <i class="fa-solid fa-newspaper"></i> Ver Noticias en la Web &rarr;
                </a>
                <a href="logout.php" style="background: #e63946; color: #fff; padding: 9px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.88rem;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>

        <!-- Tarjetas KPI / Métricas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #0077b6;">
                <div style="color: #666; font-size: 0.82rem; font-weight: 700; text-transform: uppercase;">Socios Registrados</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #023e8a; margin-top: 4px;"><?= $totalUsuarios; ?></div>
            </div>
            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #2e7d32;">
                <div style="color: #666; font-size: 0.82rem; font-weight: 700; text-transform: uppercase;">Noticias Publicadas</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #2e7d32; margin-top: 4px;"><?= $totalPublicaciones; ?></div>
            </div>
            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
                <div style="color: #666; font-size: 0.82rem; font-weight: 700; text-transform: uppercase;">Mensajes de la Hinchada</div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #d97706; margin-top: 4px;"><?= $totalMensajes; ?></div>
            </div>
        </div>

        <!-- SECCIÓN 1: CREAR NUEVA NOTICIA (MODIFICAR COSAS) -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px; margin-bottom: 35px; border-top: 4px solid #0077b6;" id="nueva-noticia">
            <h2 style="margin: 0 0 15px; font-size: 1.25rem; color: #023e8a;">
                <i class="fa-solid fa-plus-circle"></i> Publicar / Crear Nueva Noticia para el Sitio
            </h2>
            <form action="admin_panel.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="accion" value="crear_noticia">

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Título de la noticia *</label>
                        <input type="text" name="titulo" placeholder="Ej. Gimnasia y Tiro presentó a sus nuevos refuerzos" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Categoría *</label>
                        <select name="categoria_id" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Estado *</label>
                        <select name="estado_id" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                            <?php foreach ($estados as $est): ?>
                                <option value="<?= $est['id']; ?>" <?= ($est['nombre'] === 'Publicado') ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($est['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Bajada / Resumen corto</label>
                        <input type="text" name="bajada" placeholder="Breve descripción que se ve en la tarjeta" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">URL de Imagen de portada</label>
                        <input type="url" name="imagen_url" placeholder="https://ejemplo.com/foto.jpg (o dejalo vacío para default)" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                    </div>
                </div>

                <div>
                    <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Contenido completo de la noticia *</label>
                    <textarea name="contenido" rows="4" placeholder="Escribí el texto completo de la noticia aquí..." required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; font-family:inherit;"></textarea>
                </div>

                <div>
                    <button type="submit" style="background: #0077b6; color: #fff; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.95rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Publicar Noticia en el Sitio
                    </button>
                </div>
            </form>
        </div>

        <!-- SECCIÓN 2: GESTIONAR NOTICIAS EXISTENTES (EDITAR / ELIMINAR) -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px; margin-bottom: 35px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:12px;">
                <h2 style="margin:0; font-size:1.25rem; color:#023e8a;">
                    <i class="fa-solid fa-newspaper"></i> Noticias en Base de Datos (Editar o Eliminar)
                </h2>
                <span style="font-size:0.85rem; color:#666;">Total: <strong><?= count($noticias); ?></strong> noticias</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($noticias as $not): ?>
                    <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 18px; background: #fafafa;">
                        <!-- Formulario de Edición en línea de cada noticia -->
                        <form action="admin_panel.php" method="POST" style="display:flex; flex-direction:column; gap:12px;">
                            <input type="hidden" name="accion" value="editar_noticia">
                            <input type="hidden" name="noticia_id" value="<?= (int)$not['id']; ?>">

                            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="background:#0077b6; color:#fff; font-weight:800; font-size:0.75rem; padding:3px 8px; border-radius:4px;">ID #<?= (int)$not['id']; ?></span>
                                    <span style="background:#e0f2fe; color:#0369a1; font-size:0.78rem; font-weight:700; padding:3px 8px; border-radius:4px;">
                                        <?= htmlspecialchars($not['categoria']); ?>
                                    </span>
                                    <span style="background:<?= ($not['estado'] === 'Publicado') ? '#dcfce7; color:#15803d;' : '#fef3c7; color:#b45309;'; ?> font-size:0.78rem; font-weight:700; padding:3px 8px; border-radius:4px;">
                                        <?= htmlspecialchars($not['estado']); ?>
                                    </span>
                                </div>
                                <span style="font-size:0.78rem; color:#888;">Creado: <?= substr((string)$not['created_at'], 0, 10); ?></span>
                            </div>

                            <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-size:0.78rem; font-weight:700; color:#555;">Título</label>
                                    <input type="text" name="titulo" value="<?= htmlspecialchars((string)$not['titulo'], ENT_QUOTES); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box;">
                                </div>
                                <div>
                                    <label style="font-size:0.78rem; font-weight:700; color:#555;">Categoría</label>
                                    <select name="categoria_id" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box;">
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $not['categoria_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($cat['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:0.78rem; font-weight:700; color:#555;">Estado</label>
                                    <select name="estado_id" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box;">
                                        <?php foreach ($estados as $est): ?>
                                            <option value="<?= $est['id']; ?>" <?= ($est['id'] == $not['estado_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($est['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label style="font-size:0.78rem; font-weight:700; color:#555;">Bajada / Resumen</label>
                                <input type="text" name="bajada" value="<?= htmlspecialchars((string)($not['bajada'] ?? ''), ENT_QUOTES); ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box;">
                            </div>

                            <div>
                                <label style="font-size:0.78rem; font-weight:700; color:#555;">URL de Imagen</label>
                                <input type="url" name="imagen_url" value="<?= htmlspecialchars((string)($not['imagen_url'] ?? ''), ENT_QUOTES); ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box;">
                            </div>

                            <div>
                                <label style="font-size:0.78rem; font-weight:700; color:#555;">Contenido</label>
                                <textarea name="contenido" rows="2" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px; box-sizing:border-box; font-family:inherit;"><?= htmlspecialchars((string)($not['contenido'] ?? ''), ENT_QUOTES); ?></textarea>
                            </div>

                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px;">
                                <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:8px 16px; border-radius:5px; font-weight:700; cursor:pointer; font-size:0.85rem;">
                                    <i class="fa-solid fa-save"></i> Guardar Cambios de esta Noticia
                                </button>
                        </form>

                                <!-- Botón individual para Eliminar la noticia -->
                                <form action="admin_panel.php" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar la noticia #<?= $not['id']; ?>?');" style="margin:0;">
                                    <input type="hidden" name="accion" value="eliminar_noticia">
                                    <input type="hidden" name="noticia_id" value="<?= (int)$not['id']; ?>">
                                    <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:8px 14px; border-radius:5px; font-weight:600; cursor:pointer; font-size:0.85rem;">
                                        <i class="fa-solid fa-trash-can"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- SECCIÓN 3: GESTIONAR USUARIOS Y ROLES (ADMIN / USUARIO) -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px; margin-bottom: 35px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 12px;">
                <h2 style="margin: 0; font-size: 1.25rem; color: #023e8a;">
                    <i class="fa-solid fa-users-gear"></i> Padrón de Usuarios y Asignación de Roles
                </h2>
                <span style="font-size: 0.85rem; color: #666;">
                    Total: <strong><?= count($usuariosRegistrados); ?></strong> usuarios
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6; text-align: left; color: #495057;">
                            <th style="padding: 12px 14px;">ID</th>
                            <th style="padding: 12px 14px;">Nombre</th>
                            <th style="padding: 12px 14px;">Correo</th>
                            <th style="padding: 12px 14px;">Rol Actual</th>
                            <th style="padding: 12px 14px; text-align: center;">Acción de Modificación de Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuariosRegistrados as $u): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px 14px; font-weight: 700; color: #023e8a;">#<?= (int)$u['id']; ?></td>
                                <td style="padding: 12px 14px; font-weight: 600;">
                                    <?= htmlspecialchars((string)$u['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td style="padding: 12px 14px; color: #555;">
                                    <?= htmlspecialchars((string)$u['email'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td style="padding: 12px 14px;">
                                    <?php if (strtolower((string)$u['rol']) === 'admin'): ?>
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">
                                            <i class="fa-solid fa-crown"></i> ADMIN
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #e0f2fe; color: #075985; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.78rem;">
                                            <i class="fa-solid fa-user"></i> USUARIO (Solo Navega)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 14px; text-align: center;">
                                    <form action="admin_panel.php" method="POST" style="display:inline-block; margin:0;">
                                        <input type="hidden" name="accion" value="cambiar_rol">
                                        <input type="hidden" name="target_user_id" value="<?= (int)$u['id']; ?>">
                                        <?php if (strtolower((string)$u['rol']) === 'admin'): ?>
                                            <input type="hidden" name="nuevo_rol" value="usuario">
                                            <button type="submit" style="background:#f59e0b; color:#fff; border:none; padding:6px 12px; border-radius:5px; font-weight:600; cursor:pointer; font-size:0.8rem;">
                                                <i class="fa-solid fa-arrow-down"></i> Cambiar a Rol Usuario
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="nuevo_rol" value="admin">
                                            <button type="submit" style="background:#023e8a; color:#fff; border:none; padding:6px 12px; border-radius:5px; font-weight:600; cursor:pointer; font-size:0.8rem;">
                                                <i class="fa-solid fa-arrow-up"></i> Ascender a Rol Admin
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 4: MENSAJES DE CONTACTO -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px;">
            <h3 style="margin-top: 0; color: #023e8a; font-size: 1.15rem; margin-bottom: 15px;">
                <i class="fa-solid fa-envelope-open-text"></i> Mensajes Recibidos de Contacto
            </h3>
            <?php if (empty($mensajes)): ?>
                <p style="color:#777; font-size:0.9rem;">No hay mensajes registrados.</p>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($mensajes as $m): ?>
                        <div style="border:1px solid #eee; border-left:4px solid #0077b6; padding:12px 16px; border-radius:6px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                <strong><?= htmlspecialchars($m['nombre']); ?></strong> (<?= htmlspecialchars($m['email']); ?>)
                                <p style="margin:5px 0 0; color:#555; font-size:0.88rem;"><?= htmlspecialchars($m['mensaje']); ?></p>
                            </div>
                            <form action="admin_panel.php" method="POST" style="margin:0;">
                                <input type="hidden" name="accion" value="eliminar_mensaje">
                                <input type="hidden" name="mensaje_id" value="<?= (int)$m['id']; ?>">
                                <button type="submit" style="background:#fee2e2; color:#b91c1c; border:none; padding:6px 10px; border-radius:4px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                                    <i class="fa-solid fa-trash"></i> Borrar
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
