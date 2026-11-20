<?php
/**
 * historia.php
 * Vista de Historia, Estadio El Gigante del Norte y Galería de Fotos
 * Integración con Base de Datos Dinámica y Edición en Vivo para el Administrador (TP8)
 * 
 * Comportamiento por Rol:
 * - Invitado / Usuario común: SOLO NAVEGA (Lectura limpia de la historia, fotos y estadio).
 * - Administrador (fguantay39@gmail.com): Puede modificar los textos de historia, cambiar fotos
 *   del estadio y agregar o borrar fotos de la galería en tiempo real.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/db.php';

// Asegurar sesión e importar funciones de guardia
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/auth_guard.php';
}

$es_administrador = es_admin();
$mensaje_exito    = '';
$mensaje_error    = '';

// PROCESAMIENTO DE ACCIONES DE EDICIÓN EN HISTORIA Y GALERÍA (SOLO ADMIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $es_administrador) {
    $accion = $_POST['accion'] ?? '';

    // 1. EDITAR TEXTO O FOTO DE CUALQUIER SECCIÓN (HISTORIA O ESTADIO)
    if ($accion === 'editar_seccion') {
        $clave      = trim((string)($_POST['clave'] ?? ''));
        $titulo     = trim((string)($_POST['titulo'] ?? ''));
        $subtitulo  = trim((string)($_POST['subtitulo'] ?? ''));
        $contenido  = trim((string)($_POST['contenido'] ?? ''));
        $imagen_url = trim((string)($_POST['imagen_url'] ?? ''));

        if ($clave === '' || $titulo === '') {
            $mensaje_error = 'El título de la sección no puede estar vacío.';
        } else {
            try {
                $stmt = $pdo->prepare('
                    UPDATE secciones_contenido 
                    SET titulo = :titulo, subtitulo = :subtitulo, contenido = :contenido, imagen_url = :imagen_url
                    WHERE clave = :clave
                ');
                $stmt->execute([
                    'titulo'     => $titulo,
                    'subtitulo'  => $subtitulo,
                    'contenido'  => $contenido,
                    'imagen_url' => $imagen_url,
                    'clave'      => $clave,
                ]);
                $mensaje_exito = '¡Sección actualizada correctamente en la base de datos!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al actualizar: ' . $e->getMessage();
            }
        }
    }

    // 2. AGREGAR FOTO NUEVA A LA GALERÍA
    if ($accion === 'agregar_foto_galeria') {
        $titulo     = trim((string)($_POST['titulo'] ?? ''));
        $subtitulo  = trim((string)($_POST['subtitulo'] ?? ''));
        $imagen_url = trim((string)($_POST['imagen_url'] ?? ''));

        if ($imagen_url === '' || $titulo === '') {
            $mensaje_error = 'El título y la URL de la imagen son obligatorios para la galería.';
        } else {
            try {
                $nuevaClave = 'galeria_' . time();
                $stmt = $pdo->prepare('
                    INSERT INTO secciones_contenido (clave, seccion, titulo, subtitulo, contenido, imagen_url)
                    VALUES (:clave, "galeria", :titulo, :subtitulo, :contenido, :imagen_url)
                ');
                $stmt->execute([
                    'clave'      => $nuevaClave,
                    'titulo'     => $titulo,
                    'subtitulo'  => $subtitulo,
                    'contenido'  => 'Foto agregada por la administración del club',
                    'imagen_url' => $imagen_url,
                ]);
                $mensaje_exito = '¡Nueva foto agregada exitosamente a la Galería!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al agregar foto: ' . $e->getMessage();
            }
        }
    }

    // 3. ELIMINAR FOTO DE LA GALERÍA
    if ($accion === 'eliminar_foto_galeria') {
        $clave = trim((string)($_POST['clave'] ?? ''));
        if ($clave !== '') {
            try {
                $stmt = $pdo->prepare('DELETE FROM secciones_contenido WHERE clave = :clave AND seccion = "galeria"');
                $stmt->execute(['clave' => $clave]);
                $mensaje_exito = 'Foto eliminada de la galería.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar foto: ' . $e->getMessage();
            }
        }
    }
}

// Cargar contenidos desde MySQL
$seccionesMap = [];
$galeriaFotos = [];
try {
    $rows = $pdo->query('SELECT * FROM secciones_contenido')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $seccionesMap[$r['clave']] = $r;
        if ($r['seccion'] === 'galeria') {
            $galeriaFotos[] = $r;
        }
    }
} catch (PDOException $e) {
    error_log('Error leyendo secciones_contenido: ' . $e->getMessage());
}

// Helper para obtener contenido seguro
function getContenido(array $map, string $clave, string $campoDefecto, string $valDefecto): string {
    return $map[$clave][$campoDefecto] ?? $valDefecto;
}

$titulo_pagina = "Historia, Estadio y Galería | " . $nombre_sitio;
$pagina_actual = "historia";

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f8f9fa; padding-bottom: 50px;">

    <!-- MENSAJES DE ALERTA -->
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

    <!-- SECCIÓN 1: HISTORIA DEL CLUB -->
    <section id="historia" class="content-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;" id="editar-historia">
        
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:15px;">
            <div>
                <h2 style="margin:0; font-size:1.8rem; color:#023e8a;">Historia de <?= htmlspecialchars($nombre_sitio); ?></h2>
                <p style="margin:4px 0 0; color:#555;">Fundado el <strong>29 de noviembre de 1902</strong>, decano y orgullo del deporte salteño.</p>
            </div>
            <?php if ($es_administrador): ?>
                <span style="background:#e63946; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.75rem; font-weight:700;">
                    <i class="fa-solid fa-pen"></i> MODO EDICIÓN HABILITADO
                </span>
            <?php endif; ?>
        </div>

        <!-- BLOQUE HISTORIA 1: LOS ORÍGENES -->
        <?php 
            $orig_titulo = getContenido($seccionesMap, 'historia_origenes', 'titulo', 'Los Orígenes (1902)');
            $orig_sub    = getContenido($seccionesMap, 'historia_origenes', 'subtitulo', 'El nacimiento del gigante del norte');
            $orig_texto  = getContenido($seccionesMap, 'historia_origenes', 'contenido', 'El club nació bajo el nombre de Club Atlético Salteño...');
            $orig_foto   = getContenido($seccionesMap, 'historia_origenes', 'imagen_url', 'https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png');
        ?>
        <article style="background:#fff; border-radius:12px; padding:25px; margin:25px 0; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:25px; align-items:center;">
                <div>
                    <h3 style="margin-top:0; color:#023e8a; font-size:1.35rem;"><?= htmlspecialchars($orig_titulo); ?></h3>
                    <p style="font-weight:600; color:#0077b6; font-size:0.92rem; margin:5px 0 12px;"><?= htmlspecialchars($orig_sub); ?></p>
                    <p style="color:#444; line-height:1.7; font-size:0.95rem;"><?= nl2br(htmlspecialchars($orig_texto)); ?></p>
                </div>
                <div style="text-align:center;">
                    <img src="<?= htmlspecialchars($orig_foto); ?>" alt="Escudo Gimnasia y Tiro" style="max-width:180px; height:auto; margin:0 auto; display:block;">
                </div>
            </div>

            <!-- FORMULARIO DE EDICIÓN (SOLO VISIBLE PARA ADMIN) -->
            <?php if ($es_administrador): ?>
                <div style="background:#f8f9fa; border:1px dashed #0077b6; border-radius:8px; padding:15px; margin-top:20px;">
                    <strong style="color:#0077b6; font-size:0.85rem;"><i class="fa-solid fa-pen-to-square"></i> Editar Sección "Los Orígenes" (Admin):</strong>
                    <form action="historia.php" method="POST" style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                        <input type="hidden" name="accion" value="editar_seccion">
                        <input type="hidden" name="clave" value="historia_origenes">
                        
                        <input type="text" name="titulo" value="<?= htmlspecialchars($orig_titulo); ?>" placeholder="Título" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <input type="text" name="subtitulo" value="<?= htmlspecialchars($orig_sub); ?>" placeholder="Subtítulo" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <input type="url" name="imagen_url" value="<?= htmlspecialchars($orig_foto); ?>" placeholder="URL Imagen" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <textarea name="contenido" rows="3" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; font-family:inherit;"><?= htmlspecialchars($orig_texto); ?></textarea>
                        
                        <div>
                            <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:700; cursor:pointer;">
                                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios en Los Orígenes
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </article>

        <!-- BLOQUE HISTORIA 2: ASCENSOS HISTÓRICOS -->
        <?php 
            $asc_titulo = getContenido($seccionesMap, 'historia_ascensos', 'titulo', 'Ascensos Históricos a Primera División (1993 y 1997)');
            $asc_sub    = getContenido($seccionesMap, 'historia_ascensos', 'subtitulo', 'Páginas doradas en el fútbol grande');
            $asc_texto  = getContenido($seccionesMap, 'historia_ascensos', 'contenido', 'Gimnasia y Tiro escribió páginas doradas...');
            $asc_foto   = getContenido($seccionesMap, 'historia_ascensos', 'imagen_url', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80');
        ?>
        <article style="background:#fff; border-radius:12px; padding:25px; margin:25px 0; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
            <h3 style="margin-top:0; color:#023e8a; font-size:1.35rem;"><?= htmlspecialchars($asc_titulo); ?></h3>
            <p style="font-weight:600; color:#0077b6; font-size:0.92rem; margin:5px 0 12px;"><?= htmlspecialchars($asc_sub); ?></p>
            <p style="color:#444; line-height:1.7; font-size:0.95rem;"><?= nl2br(htmlspecialchars($asc_texto)); ?></p>

            <?php if ($es_administrador): ?>
                <div style="background:#f8f9fa; border:1px dashed #0077b6; border-radius:8px; padding:15px; margin-top:20px;">
                    <strong style="color:#0077b6; font-size:0.85rem;"><i class="fa-solid fa-pen-to-square"></i> Editar Sección "Ascensos" (Admin):</strong>
                    <form action="historia.php" method="POST" style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                        <input type="hidden" name="accion" value="editar_seccion">
                        <input type="hidden" name="clave" value="historia_ascensos">
                        
                        <input type="text" name="titulo" value="<?= htmlspecialchars($asc_titulo); ?>" placeholder="Título" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <input type="text" name="subtitulo" value="<?= htmlspecialchars($asc_sub); ?>" placeholder="Subtítulo" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                        <textarea name="contenido" rows="3" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; font-family:inherit;"><?= htmlspecialchars($asc_texto); ?></textarea>
                        
                        <div>
                            <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:700; cursor:pointer;">
                                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios en Ascensos
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </article>
    </section>

    <!-- SECCIÓN 2: ESTADIO EL GIGANTE DEL NORTE -->
    <?php 
        $est_titulo = getContenido($seccionesMap, 'estadio_principal', 'titulo', 'Estadio El Gigante del Norte');
        $est_sub    = getContenido($seccionesMap, 'estadio_principal', 'subtitulo', 'Ubicado en el corazón de Salta.');
        $est_texto  = getContenido($seccionesMap, 'estadio_principal', 'contenido', 'Capacidad para 25.000 espectadores...');
        $est_foto   = getContenido($seccionesMap, 'estadio_principal', 'imagen_url', 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Estadio_El_Gigante_del_Norte.jpg/1200px-Estadio_El_Gigante_del_Norte.jpg');
    ?>
    <section id="estadio" class="content-section alternate active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 20px auto 40px;">
        <h2 style="color:#023e8a; font-size:1.8rem;"><?= htmlspecialchars($est_titulo); ?></h2>
        <p style="color:#555;"><?= htmlspecialchars($est_sub); ?></p>

        <div style="text-align:center; margin: 20px 0;">
            <img src="<?= htmlspecialchars($est_foto); ?>" alt="Estadio El Gigante del Norte" style="max-width:100%; height:auto; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.15);">
        </div>

        <p style="color:#444; font-size:0.95rem; line-height:1.7;"><?= nl2br(htmlspecialchars($est_texto)); ?></p>

        <ul style="list-style:none; padding:0; display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-top:20px;">
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6; box-shadow:0 2px 6px rgba(0,0,0,0.05);"><strong>Capacidad:</strong> 25.000 espectadores</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6; box-shadow:0 2px 6px rgba(0,0,0,0.05);"><strong>Inauguración:</strong> Década de 1920</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6; box-shadow:0 2px 6px rgba(0,0,0,0.05);"><strong>Dirección:</strong> Vicente López y Virrey Toledo, Salta</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6; box-shadow:0 2px 6px rgba(0,0,0,0.05);"><strong>Superficie:</strong> Césped natural</li>
        </ul>

        <?php if ($es_administrador): ?>
            <div style="background:#f8f9fa; border:1px dashed #0077b6; border-radius:8px; padding:15px; margin-top:25px;">
                <strong style="color:#0077b6; font-size:0.85rem;"><i class="fa-solid fa-pen-to-square"></i> Modificar Datos del Estadio (Admin):</strong>
                <form action="historia.php" method="POST" style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                    <input type="hidden" name="accion" value="editar_seccion">
                    <input type="hidden" name="clave" value="estadio_principal">
                    
                    <input type="text" name="titulo" value="<?= htmlspecialchars($est_titulo); ?>" placeholder="Título Estadio" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    <input type="text" name="subtitulo" value="<?= htmlspecialchars($est_sub); ?>" placeholder="Ubicación" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    <input type="url" name="imagen_url" value="<?= htmlspecialchars($est_foto); ?>" placeholder="URL Foto Estadio" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    <textarea name="contenido" rows="2" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; font-family:inherit;"><?= htmlspecialchars($est_texto); ?></textarea>
                    
                    <div>
                        <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:700; cursor:pointer;">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios del Estadio
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </section>

    <!-- SECCIÓN 3: GALERÍA DE FOTOS (CON CARGA Y BORRADO PARA ADMIN) -->
    <section id="galeria" class="content-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
            <div>
                <h2 style="margin:0; font-size:1.8rem; color:#023e8a;">Galería Oficial del Albo</h2>
                <p style="margin:4px 0 0; color:#555;">Postales de la hinchada, momentos históricos y el Gigante del Norte.</p>
            </div>
        </div>

        <!-- FORMULARIO PARA AGREGAR FOTO A LA GALERÍA (SOLO ADMIN) -->
        <?php if ($es_administrador): ?>
            <div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:30px; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.15rem;">
                    <i class="fa-solid fa-plus-circle"></i> Agregar Nueva Foto a la Galería (Admin)
                </h3>
                <form action="historia.php" method="POST" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)) 140px; gap:12px; align-items:flex-end;">
                    <input type="hidden" name="accion" value="agregar_foto_galeria">
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Título de la foto *</label>
                        <input type="text" name="titulo" placeholder="Ej. Banderas en la Platea" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Descripción / Epígrafe</label>
                        <input type="text" name="subtitulo" placeholder="Ej. El aliento incondicional del Albo" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">URL de la imagen *</label>
                        <input type="url" name="imagen_url" placeholder="https://ejemplo.com/foto.jpg" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <button type="submit" style="width:100%; background:#0077b6; color:#fff; border:none; padding:10px; border-radius:4px; font-weight:700; cursor:pointer; font-size:0.88rem;">
                            <i class="fa-solid fa-upload"></i> Subir Foto
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- GRILLA DE FOTOS DE LA GALERÍA -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <?php foreach ($galeriaFotos as $f): ?>
                <div style="background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 3px 10px rgba(0,0,0,0.08); display:flex; flex-direction:column;">
                    <div style="height:200px; overflow:hidden;">
                        <img src="<?= htmlspecialchars($f['imagen_url']); ?>" alt="<?= htmlspecialchars($f['titulo']); ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="padding:15px; flex:1; display:flex; flex-direction:column;">
                        <h4 style="margin:0 0 6px; color:#1b263b; font-size:1.05rem;"><?= htmlspecialchars($f['titulo']); ?></h4>
                        <p style="margin:0; color:#666; font-size:0.85rem; flex:1;"><?= htmlspecialchars($f['subtitulo'] ?? ''); ?></p>
                        
                        <?php if ($es_administrador): ?>
                            <div style="margin-top:12px; padding-top:10px; border-top:1px dashed #eee; text-align:right;">
                                <form action="historia.php" method="POST" onsubmit="return confirm('¿Eliminar esta foto de la galería?');" style="margin:0;">
                                    <input type="hidden" name="accion" value="eliminar_foto_galeria">
                                    <input type="hidden" name="clave" value="<?= htmlspecialchars($f['clave']); ?>">
                                    <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:5px 10px; border-radius:4px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                                        <i class="fa-solid fa-trash"></i> Eliminar Foto
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
