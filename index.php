<?php
/**
 * index.php
 * Página de Inicio / Portada Principal con Gestión en Vivo para el Administrador
 * Trabajo Práctico N° 8 - Integración de Roles (RBAC) y CMS en la Portada
 * 
 * Comportamiento por Rol:
 * - Invitado / Usuario común: SOLO NAVEGA (ve las noticias, secciones e información sin botones de edición).
 * - Administrador (fguantay39@gmail.com): Tiene controles directos en la portada para CREAR, EDITAR y ELIMINAR
 *   noticias y cambiar roles sin necesidad de salir de la página principal.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/db.php';

// Asegurar sesión e importar funciones de guardia y rol
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/auth_guard.php';
}

$es_administrador = es_admin();
$mensaje_exito    = '';
$mensaje_error    = '';

// PROCESAMIENTO DE ACCIONES ADMINISTRATIVAS DIRECTAMENTE DESDE LA PÁGINA PRINCIPAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $es_administrador) {
    $accion = $_POST['accion'] ?? '';

    // 1. CREAR NOTICIA DESDE LA PORTADA
    if ($accion === 'crear_noticia') {
        $titulo       = trim((string)($_POST['titulo'] ?? ''));
        $categoria_id = (int)($_POST['categoria_id'] ?? 1);
        $bajada       = trim((string)($_POST['bajada'] ?? ''));
        $contenido    = trim((string)($_POST['contenido'] ?? ''));
        $imagen_url   = trim((string)($_POST['imagen_url'] ?? ''));

        if ($titulo === '' || $contenido === '') {
            $mensaje_error = 'El título y el contenido son obligatorios.';
        } else {
            try {
                // Generar slug
                $slug = preg_replace('/[^a-z0-9\-]/', '-', mb_strtolower($titulo, 'UTF-8')) . '-' . time();
                $slug = trim(preg_replace('/-+/', '-', $slug), '-');

                $stmt = $pdo->prepare('
                    INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, slug, bajada, contenido, imagen_url)
                    VALUES (:usuario_id, :categoria_id, 2, :titulo, :slug, :bajada, :contenido, :imagen_url)
                ');
                $stmt->execute([
                    'usuario_id'   => (int)$_SESSION['usuario_id'],
                    'categoria_id' => $categoria_id,
                    'titulo'       => $titulo,
                    'slug'         => $slug,
                    'bajada'       => $bajada,
                    'contenido'    => $contenido,
                    'imagen_url'   => !empty($imagen_url) ? $imagen_url : 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80',
                ]);
                $mensaje_exito = '¡Nueva noticia publicada con éxito en la portada!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al crear la noticia: ' . $e->getMessage();
            }
        }
    }

    // 2. EDITAR NOTICIA DESDE LA PORTADA
    if ($accion === 'editar_noticia') {
        $id         = (int)($_POST['noticia_id'] ?? 0);
        $titulo     = trim((string)($_POST['titulo'] ?? ''));
        $bajada     = trim((string)($_POST['bajada'] ?? ''));
        $contenido  = trim((string)($_POST['contenido'] ?? ''));
        $imagen_url = trim((string)($_POST['imagen_url'] ?? ''));

        if ($id > 0 && $titulo !== '') {
            try {
                $stmt = $pdo->prepare('
                    UPDATE publicaciones 
                    SET titulo = :titulo, bajada = :bajada, contenido = :contenido, 
                        imagen_url = :imagen_url, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ');
                $stmt->execute([
                    'id'         => $id,
                    'titulo'     => $titulo,
                    'bajada'     => $bajada,
                    'contenido'  => $contenido,
                    'imagen_url' => $imagen_url,
                ]);
                $mensaje_exito = '¡Noticia #' . $id . ' actualizada exitosamente!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al editar la noticia: ' . $e->getMessage();
            }
        }
    }

    // 3. ELIMINAR NOTICIA DESDE LA PORTADA
    if ($accion === 'eliminar_noticia') {
        $id = (int)($_POST['noticia_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare('DELETE FROM publicacion_etiquetas WHERE publicacion_id = :id')->execute(['id' => $id]);
                $pdo->prepare('DELETE FROM publicaciones WHERE id = :id')->execute(['id' => $id]);
                $mensaje_exito = 'Noticia #' . $id . ' eliminada de la portada y de la base de datos.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar: ' . $e->getMessage();
            }
        }
    }

    // 4. CAMBIAR ROL DE USUARIO DESDE LA PORTADA
    if ($accion === 'cambiar_rol') {
        $target_id = (int)($_POST['target_user_id'] ?? 0);
        $nuevo_rol = strtolower(trim((string)($_POST['nuevo_rol'] ?? 'usuario')));
        if ($target_id === (int)$_SESSION['usuario_id'] && $nuevo_rol !== 'admin') {
            $mensaje_error = 'No podés quitarte el rol de administrador a vos mismo.';
        } else {
            try {
                $rol_fk = ($nuevo_rol === 'admin') ? 1 : 3;
                $stmt = $pdo->prepare('UPDATE usuarios SET rol = :rol, rol_id = :rol_id WHERE id = :id');
                $stmt->execute(['rol' => $nuevo_rol, 'rol_id' => $rol_fk, 'id' => $target_id]);
                $mensaje_exito = 'Rol del usuario #' . $target_id . ' actualizado a: ' . strtoupper($nuevo_rol);
            } catch (PDOException $e) {
                $mensaje_error = 'Error al cambiar rol: ' . $e->getMessage();
            }
        }
    }
}

// Cargar noticias desde la base de datos para mostrarlas en la portada
$noticias_portada = [];
$categorias_list  = [];
$usuarios_list    = [];
try {
    $noticias_portada = $pdo->query('
        SELECT p.id, p.titulo, p.bajada, p.contenido, p.imagen_url, p.created_at, c.nombre as categoria
        FROM publicaciones p
        INNER JOIN categorias c ON p.categoria_id = c.id
        WHERE p.estado_id = 2
        ORDER BY p.id DESC
        LIMIT 6
    ')->fetchAll();

    $categorias_list = $pdo->query('SELECT id, nombre FROM categorias')->fetchAll();
    
    if ($es_administrador) {
        $usuarios_list = $pdo->query('SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC')->fetchAll();
    }
} catch (PDOException $e) {
    error_log('Error cargando datos de portada: ' . $e->getMessage());
}

$titulo_pagina = "Inicio - " . $nombre_sitio;
$pagina_actual = "inicio";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- BARRA FLOTANTE / SUPERIOR PARA EL ADMINISTRADOR (SOLO VISIBLE CON fguantay39@gmail.com) -->
    <?php if ($es_administrador): ?>
        <div style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #fff; padding: 18px 25px; border-bottom: 4px solid #0077b6; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <span style="background: #e63946; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                        <i class="fa-solid fa-crown"></i> Modo Administrador Activo
                    </span>
                    <h2 style="margin: 6px 0 0; font-size: 1.25rem;">Panel de Edición en Vivo — Portada</h2>
                    <p style="margin: 2px 0 0; font-size: 0.85rem; opacity: 0.85;">
                        Conectado como <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Franco'); ?></strong> (<code><?= htmlspecialchars($_SESSION['usuario_email'] ?? 'fguantay39@gmail.com'); ?></code>). Podés editar todo desde acá.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="#editor-noticia-principal" style="background: #0077b6; color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 0.88rem;">
                        <i class="fa-solid fa-plus-circle"></i> + Publicar Noticia Aquí
                    </a>
                    <a href="#gestion-usuarios-portada" style="background: rgba(255,255,255,0.15); color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.88rem;">
                        <i class="fa-solid fa-users-gear"></i> Gestionar Roles
                    </a>
                    <a href="admin_panel.php" style="background: #023e8a; color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.88rem;">
                        <i class="fa-solid fa-sliders"></i> Panel Extendido &rarr;
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ALERTAS DE ÉXITO O ERROR TRAS MODIFICAR -->
    <?php if (!empty($mensaje_exito)): ?>
        <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
            <div style="background: #e8f5e9; border-left: 5px solid #2e7d32; color: #1b5e20; padding: 14px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
            <div style="background: #ffebee; border-left: 5px solid #d32f2f; color: #c62828; padding: 14px 20px; border-radius: 8px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN HERO / BIENVENIDA -->
    <section id="inicio" class="hero-banner">
        <div class="hero-content">
            <span class="badge-new">BIENVENIDOS A LA PÁGINA DEL HINCHA</span>
            <a href="index.php">
                <img src="https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png" alt="Escudo oficial del club Gimnasia y Tiro de Salta" style="width: 120px; height: auto; display: block; margin: 25px auto;">
            </a>
            <h1>Bienvenido a <?= htmlspecialchars($nombre_sitio); ?></h1>
            <p>La historia, la pasión y la actualidad del club más grande de Salta.</p>
            <div style="margin-top: 15px;">
                <span class="badge-env" style="background:#0077b6; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.85rem; font-weight:600;">
                    Entorno: <?= htmlspecialchars(strtoupper($entorno_app)); ?>
                </span>
            </div>
        </div>
        <div class="section-footer"></div>
    </section>

    <!-- FORMULARIO DE CREACIÓN EN LA PORTADA (SOLO PARA ADMIN) -->
    <?php if ($es_administrador): ?>
        <section id="editor-noticia-principal" style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
            <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 25px; border-top: 4px solid #0077b6;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.25rem;">
                    <i class="fa-solid fa-plus-circle"></i> Publicar Nueva Noticia Directamente en la Portada
                </h3>
                <form action="index.php" method="POST" style="display:flex; flex-direction:column; gap:14px;">
                    <input type="hidden" name="accion" value="crear_noticia">

                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Título de la noticia *</label>
                            <input type="text" name="titulo" placeholder="Ej. Próximo partido en El Gigante del Norte confirmado" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Categoría *</label>
                            <select name="categoria_id" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                                <?php foreach ($categorias_list as $cat): ?>
                                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Bajada / Resumen corto</label>
                            <input type="text" name="bajada" placeholder="Texto corto que resume la noticia" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">URL de Imagen</label>
                            <input type="url" name="imagen_url" placeholder="https://ejemplo.com/foto.jpg" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Contenido completo *</label>
                        <textarea name="contenido" rows="3" placeholder="Redactá la noticia completa aquí..." required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; font-family:inherit;"></textarea>
                    </div>

                    <div>
                        <button type="submit" style="background: #0077b6; color: #fff; padding: 10px 22px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.95rem;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Publicar en Portada
                        </button>
                    </div>
                </form>
            </div>
        </section>
    <?php endif; ?>

    <!-- SECCIÓN DE NOTICIAS DE LA PORTADA (CON BOTONES DE EDICIÓN PARA ADMIN) -->
    <section class="content-section" style="display:block; padding: 20px 20px 40px; max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <div>
                <h2 style="margin:0; font-size:1.6rem; color:#023e8a;">Últimas Noticias de Gimnasia y Tiro</h2>
                <p style="margin:4px 0 0; color:#666;">
                    <?= $es_administrador ? 'Como administrador podés editar o borrar cualquiera de estas noticias en el acto.' : 'Enterate del día a día del club más grande del norte argentino.'; ?>
                </p>
            </div>
            <a href="noticias.php" style="color:#0077b6; font-weight:700; text-decoration:none; font-size:0.95rem;">
                Ver todas las noticias &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">
            <?php foreach ($noticias_portada as $not): ?>
                <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.07); overflow: hidden; display: flex; flex-direction: column; border-top: 4px solid #0077b6;">
                    
                    <div style="height: 180px; overflow: hidden; position: relative;">
                        <img src="<?= htmlspecialchars(!empty($not['imagen_url']) ? $not['imagen_url'] : 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80'); ?>" alt="<?= htmlspecialchars($not['titulo']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <span style="position: absolute; top: 12px; left: 12px; background: rgba(0,119,182,0.9); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 12px; text-transform: uppercase;">
                            <?= htmlspecialchars($not['categoria']); ?>
                        </span>
                    </div>

                    <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                        <h3 style="margin: 0 0 10px; font-size: 1.15rem; color: #1b263b;">
                            <?= htmlspecialchars($not['titulo']); ?>
                        </h3>
                        <p style="margin: 0 0 15px; color: #555; font-size: 0.9rem; line-height: 1.5; flex: 1;">
                            <?= htmlspecialchars(!empty($not['bajada']) ? $not['bajada'] : substr($not['contenido'], 0, 140) . '...'); ?>
                        </p>

                        <!-- HERRAMIENTAS DE EDICIÓN EXCLUSIVAS DEL ADMIN EN LA PORTADA -->
                        <?php if ($es_administrador): ?>
                            <div style="background: #f8f9fa; border: 1px dashed #0077b6; border-radius: 8px; padding: 12px; margin-top: 15px;">
                                <strong style="color: #0077b6; font-size: 0.8rem; display: block; margin-bottom: 8px;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edición Rápida (Admin):
                                </strong>
                                
                                <!-- Formulario de Modificación en Línea -->
                                <form action="index.php" method="POST" style="display:flex; flex-direction:column; gap:8px;">
                                    <input type="hidden" name="accion" value="editar_noticia">
                                    <input type="hidden" name="noticia_id" value="<?= (int)$not['id']; ?>">

                                    <input type="text" name="titulo" value="<?= htmlspecialchars((string)$not['titulo'], ENT_QUOTES); ?>" placeholder="Modificar Título" style="width:100%; padding:6px; font-size:0.82rem; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                                    
                                    <input type="text" name="bajada" value="<?= htmlspecialchars((string)($not['bajada'] ?? ''), ENT_QUOTES); ?>" placeholder="Modificar Bajada" style="width:100%; padding:6px; font-size:0.82rem; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                                    
                                    <input type="url" name="imagen_url" value="<?= htmlspecialchars((string)($not['imagen_url'] ?? ''), ENT_QUOTES); ?>" placeholder="URL Imagen" style="width:100%; padding:6px; font-size:0.82rem; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">

                                    <textarea name="contenido" rows="2" placeholder="Contenido" style="width:100%; padding:6px; font-size:0.82rem; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;"><?= htmlspecialchars((string)($not['contenido'] ?? ''), ENT_QUOTES); ?></textarea>

                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                        <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:6px 12px; border-radius:4px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                                            <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                                        </button>
                                </form>

                                        <!-- Botón de Borrado de Noticia -->
                                        <form action="index.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?');" style="margin:0;">
                                            <input type="hidden" name="accion" value="eliminar_noticia">
                                            <input type="hidden" name="noticia_id" value="<?= (int)$not['id']; ?>">
                                            <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:6px 10px; border-radius:4px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECCIÓN DE ACCESOS RÁPIDOS A SECCIONES (PARA NAVEGAR) -->
    <section class="content-section" style="display:block; padding: 20px 20px 40px; max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align:center; margin-bottom: 10px;">Secciones del Sitio</h2>
        <p style="text-align:center; margin-bottom: 30px; color:#555;">Explorá los diferentes módulos del club desarrollados con arquitectura PHP Server-Side Rendering (SSR).</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">📰 Noticias</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Enterate del último partido y el fixture de la Primera Nacional.</p>
                <a href="noticias.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Noticias &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">👥 Plantel</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Conocé a los jugadores del plantel profesional y cuerpo técnico.</p>
                <a href="plantel.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Plantel &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">⚽ Partidos y Tabla</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Fixture completo y tabla de posiciones actualizada.</p>
                <a href="partidos.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Partidos &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">🏛️ Historia y Estadio</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Desde la fundación en 1902 hasta el Gigante del Norte.</p>
                <a href="historia.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Historia &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">📱 Contacto</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Envianos tu mensaje o escribinos al correo oficial del club.</p>
                <a href="contacto.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ir a Contacto &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">🔐 Comunidad Alba</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Accedé a tu perfil de socio y carnet digital interactivo.</p>
                <a href="<?= isset($_SESSION['usuario_id']) ? 'dashboard.php' : 'login.php'; ?>" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">
                    <?= isset($_SESSION['usuario_id']) ? 'Mi Carnet / Perfil &rarr;' : 'Ingresar / Registro &rarr;'; ?>
                </a>
            </div>
        </div>
    </section>

    <!-- GESTIÓN RÁPIDA DE ROLES DE USUARIOS EN LA PORTADA (SOLO VISIBLE PARA EL ADMIN) -->
    <?php if ($es_administrador && !empty($usuarios_list)): ?>
        <section id="gestion-usuarios-portada" style="max-width: 1200px; margin: 20px auto 40px; padding: 0 20px;">
            <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 25px; border-top: 4px solid #1b263b;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.25rem;">
                    <i class="fa-solid fa-users-gear"></i> Gestión Rápida de Roles de Usuarios (Admin)
                </h3>
                <p style="color:#666; font-size:0.88rem; margin-bottom:15px;">
                    Cambiá los roles directamente desde la portada. Los usuarios con rol <strong>USUARIO</strong> solo podrán navegar.
                </p>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                        <thead>
                            <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6; text-align:left;">
                                <th style="padding:10px;">ID</th>
                                <th style="padding:10px;">Nombre</th>
                                <th style="padding:10px;">Correo</th>
                                <th style="padding:10px;">Rol</th>
                                <th style="padding:10px; text-align:center;">Acción de Cambio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios_list as $u): ?>
                                <tr style="border-bottom:1px solid #eee;">
                                    <td style="padding:10px; font-weight:700;">#<?= (int)$u['id']; ?></td>
                                    <td style="padding:10px; font-weight:600;"><?= htmlspecialchars((string)$u['nombre']); ?></td>
                                    <td style="padding:10px; color:#555;"><?= htmlspecialchars((string)$u['email']); ?></td>
                                    <td style="padding:10px;">
                                        <?php if (strtolower((string)$u['rol']) === 'admin'): ?>
                                            <span style="background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:10px; font-weight:700; font-size:0.75rem;">ADMIN</span>
                                        <?php else: ?>
                                            <span style="background:#e0f2fe; color:#075985; padding:3px 8px; border-radius:10px; font-weight:600; font-size:0.75rem;">USUARIO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:10px; text-align:center;">
                                        <form action="index.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="accion" value="cambiar_rol">
                                            <input type="hidden" name="target_user_id" value="<?= (int)$u['id']; ?>">
                                            <?php if (strtolower((string)$u['rol']) === 'admin'): ?>
                                                <input type="hidden" name="nuevo_rol" value="usuario">
                                                <button type="submit" style="background:#f59e0b; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">
                                                    Cambiar a Rol Usuario
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="nuevo_rol" value="admin">
                                                <button type="submit" style="background:#023e8a; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:0.75rem; font-weight:600; cursor:pointer;">
                                                    Ascender a Rol Admin
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
        </section>
    <?php endif; ?>

    <div class="section-footer" style="margin-top: 40px;"><hr><p>Inicio | Portal Oficial de Club Atlético Gimnasia y Tiro de Salta</p></div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
