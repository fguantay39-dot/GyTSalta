<?php
/**
 * noticias.php
 * Vista de Noticias del Club - Integración con Base de Datos y Roles (TP8)
 * 
 * Lógica de Roles:
 * - Usuario común o invitado: SOLO NAVEGA (Lectura limpia de novedades).
 * - Administrador (fguantay39@gmail.com): Puede crear, editar y eliminar noticias en el acto.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/db.php';

// Asegurar carga de sesión para identificar rol
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/auth_guard.php';
}

$es_administrador = es_admin();

// Obtener publicaciones publicadas desde MySQL
$noticias_db = [];
try {
    $stmt = $pdo->query('
        SELECT p.id, p.titulo, p.bajada, p.contenido, p.imagen_url, p.created_at, c.nombre AS categoria
        FROM publicaciones p
        INNER JOIN categorias c ON p.categoria_id = c.id
        WHERE p.estado_id = 2
        ORDER BY p.id DESC
    ');
    $noticias_db = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error cargando noticias: ' . $e->getMessage());
}

$titulo_pagina = 'Noticias - ' . $nombre_sitio;
$pagina_actual = 'noticias';

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="noticias" class="noticias-container target-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        
        <!-- BARRA EXCLUSIVA DE ADMINISTRADOR (fguantay39@gmail.com) -->
        <?php if ($es_administrador): ?>
            <div style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #fff; padding: 18px 24px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <div>
                    <span style="background: #e63946; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                        <i class="fa-solid fa-crown"></i> Modo Administrador Activo
                    </span>
                    <h3 style="margin: 6px 0 0; font-size: 1.15rem;">Hola Franco, podés modificar el contenido de esta sección</h3>
                    <p style="margin: 3px 0 0; font-size: 0.85rem; opacity: 0.85;">Los usuarios estándar solo pueden navegar; vos podés publicar, editar y borrar.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="admin_panel.php#nueva-noticia" style="background: #0077b6; color: #fff; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 0.88rem;">
                        <i class="fa-solid fa-plus-circle"></i> + Nueva Noticia
                    </a>
                    <a href="admin_panel.php" style="background: rgba(255,255,255,0.15); color: #fff; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.88rem;">
                        <i class="fa-solid fa-sliders"></i> Panel de Control
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="titulo-seccion">Noticias de <?= htmlspecialchars($nombre_sitio); ?></h2>
        <p class="subtitulo-seccion">Últimas novedades, resultados y actualidad del Albo salteño.</p>

        <div class="grid-noticias">
            <?php if (!empty($noticias_db)): ?>
                <?php foreach ($noticias_db as $not): ?>
                    <article class="card-noticia" style="position: relative; display: flex; flex-direction: column;">
                        <div class="card-imagen">
                            <img src="<?= htmlspecialchars(!empty($not['imagen_url']) ? $not['imagen_url'] : 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80'); ?>" alt="<?= htmlspecialchars($not['titulo']); ?>" loading="lazy">
                        </div>
                        <div class="card-contenido" style="flex: 1; display: flex; flex-direction: column;">
                            <span class="etiqueta-noticia"><?= htmlspecialchars(strtoupper($not['categoria'])); ?></span>
                            <h3><?= htmlspecialchars($not['titulo']); ?></h3>
                            <p><?= htmlspecialchars($not['bajada'] ?: substr($not['contenido'], 0, 150) . '...'); ?></p>
                            
                            <!-- CONTROLES EXCLUSIVOS PARA EL ADMINISTRADOR -->
                            <?php if ($es_administrador): ?>
                                <div style="margin-top: auto; padding-top: 15px; border-top: 1px dashed #ccc; display: flex; justify-content: space-between; align-items: center;">
                                    <a href="admin_panel.php" style="background: #023e8a; color: #fff; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fa-solid fa-pen-to-square"></i> Editar en Panel
                                    </a>
                                    <form action="admin_panel.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?');" style="margin: 0;">
                                        <input type="hidden" name="accion" value="eliminar_noticia">
                                        <input type="hidden" name="noticia_id" value="<?= (int)$not['id']; ?>">
                                        <button type="submit" style="background: #dc2626; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback a noticias iniciales si la tabla estuviera vacía -->
                <article class="card-noticia">
                    <div class="card-imagen">
                        <img src="https://flex-app.tadevel-cdn.com/hostname/informatesalta.com.ar/api/v1/resizer?asset=6a8a053949e2a0881bb41c74&format=jpeg&width=1200&height=675&focuspoint=0.6031746031746031%2C0.5767483428911894&v=e1d860d7e6b2646b001a4b05b18de61d&s=ebea05a9dd07e631d91bf791a5382d65" alt="Gimnasia y Tiro de Salta">
                    </div>
                    <div class="card-contenido">
                        <span class="etiqueta-noticia">ÚLTIMO PARTIDO</span>
                        <h3>Gimnasia y Tiro cayó en San Juan y perdió su invicto</h3>
                        <p>El Albo perdió ante San Martín de San Juan y puso fin a una racha de 8 partidos sin derrotas. Ahora el equipo buscará recuperarse en el Gigante del Norte.</p>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
