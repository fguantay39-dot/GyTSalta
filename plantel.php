<?php
/**
 * plantel.php
 * Vista del Plantel Profesional - Integración con Base de Datos y Edición Total (Admin)
 * 
 * Lógica de Roles:
 * - Usuario común / Invitado: SOLO NAVEGA (Lectura limpia de los futbolistas y DT).
 * - Administrador (fguantay39@gmail.com): Puede agregar jugadores, editar sus datos o borrarlos.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/auth_guard.php';
}

$es_administrador = es_admin();
$mensaje_exito    = '';
$mensaje_error    = '';

// ACCIONES ADMINISTRATIVAS PARA EL PLANTEL (SOLO ADMIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $es_administrador) {
    $accion = $_POST['accion'] ?? '';

    // 1. AGREGAR JUGADOR
    if ($accion === 'agregar_jugador') {
        $nombre     = trim((string)($_POST['nombre'] ?? ''));
        $categoria  = trim((string)($_POST['categoria'] ?? 'Mediocampistas'));
        $posicion   = trim((string)($_POST['posicion'] ?? ''));
        $imagen_url = trim((string)($_POST['imagen_url'] ?? ''));
        $dorsal     = !empty($_POST['dorsal']) ? (int)$_POST['dorsal'] : null;

        if ($nombre === '' || $posicion === '') {
            $mensaje_error = 'El nombre y la posición son obligatorios.';
        } else {
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO plantel_jugadores (categoria, nombre, posicion, imagen_url, dorsal)
                    VALUES (:cat, :nom, :pos, :img, :dor)
                ');
                $stmt->execute([
                    'cat' => $categoria,
                    'nom' => $nombre,
                    'pos' => $posicion,
                    'img' => !empty($imagen_url) ? $imagen_url : 'https://images.fotmob.com/image_resources/playerimages/361668.png',
                    'dor' => $dorsal,
                ]);
                $mensaje_exito = '¡Jugador ' . htmlspecialchars($nombre) . ' agregado al plantel!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al agregar jugador: ' . $e->getMessage();
            }
        }
    }

    // 2. EDITAR JUGADOR
    if ($accion === 'editar_jugador') {
        $id         = (int)($_POST['jugador_id'] ?? 0);
        $nombre     = trim((string)($_POST['nombre'] ?? ''));
        $posicion   = trim((string)($_POST['posicion'] ?? ''));
        $categoria  = trim((string)($_POST['categoria'] ?? ''));
        $imagen_url = trim((string)($_POST['imagen_url'] ?? ''));

        if ($id > 0 && $nombre !== '') {
            try {
                $stmt = $pdo->prepare('
                    UPDATE plantel_jugadores 
                    SET nombre = :nom, posicion = :pos, categoria = :cat, imagen_url = :img 
                    WHERE id = :id
                ');
                $stmt->execute([
                    'nom' => $nombre,
                    'pos' => $posicion,
                    'cat' => $categoria,
                    'img' => $imagen_url,
                    'id'  => $id,
                ]);
                $mensaje_exito = 'Datos del jugador actualizados con éxito.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al actualizar: ' . $e->getMessage();
            }
        }
    }

    // 3. ELIMINAR JUGADOR
    if ($accion === 'eliminar_jugador') {
        $id = (int)($_POST['jugador_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare('DELETE FROM plantel_jugadores WHERE id = :id')->execute(['id' => $id]);
                $mensaje_exito = 'Jugador eliminado del plantel.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar jugador: ' . $e->getMessage();
            }
        }
    }
}

// Cargar jugadores agrupados por categoría desde MySQL
$plantelPorCat = [];
try {
    $todosJugadores = $pdo->query('SELECT * FROM plantel_jugadores ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($todosJugadores as $j) {
        $cat = $j['categoria'];
        if (!isset($plantelPorCat[$cat])) {
            $plantelPorCat[$cat] = [];
        }
        $plantelPorCat[$cat][] = $j;
    }
} catch (PDOException $e) {
    error_log('Error leyendo plantel_jugadores: ' . $e->getMessage());
}

$titulo_pagina = "Plantel Oficial - " . $nombre_sitio;
$pagina_actual = "plantel";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="plantel" class="content-section alternate active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:15px;">
            <div>
                <h2 style="margin:0; font-size:1.8rem; color:#023e8a;">Plantel Profesional de <?= htmlspecialchars($nombre_sitio); ?></h2>
                <p style="margin:4px 0 0; color:#555;">Conocé a los protagonistas que defienden la camiseta del Albo en la temporada actual.</p>
            </div>
            <?php if ($es_administrador): ?>
                <span style="background:#e63946; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.75rem; font-weight:700;">
                    <i class="fa-solid fa-pen"></i> MODO EDICIÓN HABILITADO
                </span>
            <?php endif; ?>
        </div>

        <!-- ALERTAS -->
        <?php if (!empty($mensaje_exito)): ?>
            <div style="background:#e8f5e9; border-left:5px solid #2e7d32; color:#1b5e20; padding:12px 18px; border-radius:6px; font-weight:600; margin-bottom:20px;">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div style="background:#ffebee; border-left:5px solid #d32f2f; color:#c62828; padding:12px 18px; border-radius:6px; font-weight:600; margin-bottom:20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error); ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO PARA AGREGAR JUGADOR (SOLO ADMIN) -->
        <?php if ($es_administrador): ?>
            <div style="background:#fff; border-radius:12px; padding:22px; margin-bottom:35px; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.15rem;">
                    <i class="fa-solid fa-user-plus"></i> Agregar Nuevo Futbolista o Integrante del Plantel (Admin)
                </h3>
                <form action="plantel.php" method="POST" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 140px; gap:12px; align-items:flex-end;">
                    <input type="hidden" name="accion" value="agregar_jugador">
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Nombre y Apellido *</label>
                        <input type="text" name="nombre" placeholder="Ej. Matías Birge" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Categoría *</label>
                        <select name="categoria" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                            <option value="Arqueros">Arqueros</option>
                            <option value="Defensores">Defensores</option>
                            <option value="Mediocampistas" selected>Mediocampistas</option>
                            <option value="Delanteros">Delanteros</option>
                            <option value="Director Técnico">Director Técnico</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Posición *</label>
                        <input type="text" name="posicion" placeholder="Ej. Volante Central" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">URL de Foto</label>
                        <input type="url" name="imagen_url" placeholder="https://ejemplo.com/foto.png" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <button type="submit" style="width:100%; background:#0077b6; color:#fff; border:none; padding:10px; border-radius:4px; font-weight:700; cursor:pointer;">
                            <i class="fa-solid fa-plus"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- GRILLA DE JUGADORES POR CATEGORÍA -->
        <div class="players-container" style="display:flex; flex-direction:column; gap:40px;">
            <?php foreach ($plantelPorCat as $categoriaNombre => $listaJugadores): ?>
                <div class="players-category">
                    <h3 style="border-bottom:2px solid #0077b6; padding-bottom:8px; color:#023e8a; font-size:1.35rem; margin-bottom:20px;">
                        <?= htmlspecialchars($categoriaNombre); ?>
                    </h3>
                    
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px;">
                        <?php foreach ($listaJugadores as $jug): ?>
                            <div style="background:#fff; border-radius:10px; padding:18px; text-align:center; box-shadow:0 3px 10px rgba(0,0,0,0.06); display:flex; flex-direction:column; justify-content:space-between; border-top:3px solid #0077b6;">
                                <div>
                                    <img src="<?= htmlspecialchars($jug['imagen_url'] ?: 'https://images.fotmob.com/image_resources/playerimages/361668.png'); ?>" alt="<?= htmlspecialchars($jug['nombre']); ?>" style="width:110px; height:110px; object-fit:cover; border-radius:50%; margin:0 auto 12px; display:block; background:#f0f4f8; border:2px solid #e0e0e0;">
                                    <h4 style="margin:0 0 4px; font-size:1.05rem; color:#1b263b;"><?= htmlspecialchars($jug['nombre']); ?></h4>
                                    <p style="margin:0; color:#0077b6; font-weight:600; font-size:0.85rem;"><?= htmlspecialchars($jug['posicion']); ?></p>
                                </div>

                                <!-- HERRAMIENTAS DE EDICIÓN PARA ADMIN -->
                                <?php if ($es_administrador): ?>
                                    <div style="margin-top:15px; padding-top:10px; border-top:1px dashed #eee;">
                                        <form action="plantel.php" method="POST" style="display:flex; flex-direction:column; gap:6px; margin-bottom:6px;">
                                            <input type="hidden" name="accion" value="editar_jugador">
                                            <input type="hidden" name="jugador_id" value="<?= (int)$jug['id']; ?>">
                                            <input type="hidden" name="categoria" value="<?= htmlspecialchars($jug['categoria']); ?>">
                                            
                                            <input type="text" name="nombre" value="<?= htmlspecialchars($jug['nombre']); ?>" style="width:100%; padding:4px 6px; font-size:0.75rem; border:1px solid #ccc; border-radius:3px; box-sizing:border-box;">
                                            <input type="text" name="posicion" value="<?= htmlspecialchars($jug['posicion']); ?>" style="width:100%; padding:4px 6px; font-size:0.75rem; border:1px solid #ccc; border-radius:3px; box-sizing:border-box;">
                                            <input type="url" name="imagen_url" value="<?= htmlspecialchars($jug['imagen_url']); ?>" style="width:100%; padding:4px 6px; font-size:0.75rem; border:1px solid #ccc; border-radius:3px; box-sizing:border-box;">
                                            
                                            <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:4px; border-radius:3px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                                                <i class="fa-solid fa-save"></i> Guardar
                                            </button>
                                        </form>

                                        <form action="plantel.php" method="POST" onsubmit="return confirm('¿Borrar a <?= htmlspecialchars($jug['nombre']); ?> del plantel?');" style="margin:0;">
                                            <input type="hidden" name="accion" value="eliminar_jugador">
                                            <input type="hidden" name="jugador_id" value="<?= (int)$jug['id']; ?>">
                                            <button type="submit" style="width:100%; background:#dc2626; color:#fff; border:none; padding:3px; border-radius:3px; font-size:0.72rem; font-weight:700; cursor:pointer;">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section-footer" style="margin-top:40px;">
            <hr>
            <p>Plantel Oficial | La fuerza y pasión de Gimnasia y Tiro de Salta</p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
