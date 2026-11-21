<?php
/**
 * partidos.php
 * Vista de Fixture, Partidos y Tabla de Posiciones - Edición Total para Administrador
 * 
 * Lógica de Roles:
 * - Usuario / Invitado: SOLO NAVEGA (Lectura limpia del fixture y la tabla).
 * - Administrador (fguantay39@gmail.com): Puede agregar partidos, editar resultados, cambiar horarios
 *   y actualizar puntos o partidos jugados en la tabla de posiciones.
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

// PROCESAMIENTO DE ACCIONES ADMINISTRATIVAS (SOLO ADMIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $es_administrador) {
    $accion = $_POST['accion'] ?? '';

    // 1. AGREGAR PARTIDO AL FIXTURE
    if ($accion === 'agregar_partido') {
        $fecha     = trim((string)($_POST['fecha'] ?? ''));
        $rival     = trim((string)($_POST['rival'] ?? ''));
        $condicion = trim((string)($_POST['condicion'] ?? ''));
        $horario   = trim((string)($_POST['horario'] ?? ''));

        if ($fecha === '' || $rival === '') {
            $mensaje_error = 'La fecha y el rival son obligatorios.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO partidos_fixture (fecha, rival, condicion, horario) VALUES (?, ?, ?, ?)');
                $stmt->execute([$fecha, $rival, $condicion, $horario]);
                $mensaje_exito = '¡Partido contra ' . htmlspecialchars($rival) . ' agregado al fixture!';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al agregar partido: ' . $e->getMessage();
            }
        }
    }

    // 2. EDITAR PARTIDO
    if ($accion === 'editar_partido') {
        $id        = (int)($_POST['partido_id'] ?? 0);
        $fecha     = trim((string)($_POST['fecha'] ?? ''));
        $rival     = trim((string)($_POST['rival'] ?? ''));
        $condicion = trim((string)($_POST['condicion'] ?? ''));
        $horario   = trim((string)($_POST['horario'] ?? ''));

        if ($id > 0 && $rival !== '') {
            try {
                $stmt = $pdo->prepare('UPDATE partidos_fixture SET fecha = ?, rival = ?, condicion = ?, horario = ? WHERE id = ?');
                $stmt->execute([$fecha, $rival, $condicion, $horario, $id]);
                $mensaje_exito = 'Partido actualizado.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al actualizar: ' . $e->getMessage();
            }
        }
    }

    // 3. ELIMINAR PARTIDO
    if ($accion === 'eliminar_partido') {
        $id = (int)($_POST['partido_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->prepare('DELETE FROM partidos_fixture WHERE id = ?')->execute([$id]);
                $mensaje_exito = 'Partido eliminado del fixture.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al eliminar: ' . $e->getMessage();
            }
        }
    }

    // 4. ACTUALIZAR EQUIPO EN TABLA DE POSICIONES
    if ($accion === 'editar_posicion') {
        $pos    = (int)($_POST['posicion'] ?? 0);
        $equipo = trim((string)($_POST['equipo'] ?? ''));
        $puntos = (int)($_POST['puntos'] ?? 0);
        $pj     = (int)($_POST['pj'] ?? 0);

        if ($pos > 0 && $equipo !== '') {
            try {
                $stmt = $pdo->prepare('UPDATE tabla_posiciones SET equipo = ?, puntos = ?, pj = ? WHERE posicion = ?');
                $stmt->execute([$equipo, $puntos, $pj, $pos]);
                $mensaje_exito = 'Posición #' . $pos . ' (' . htmlspecialchars($equipo) . ') actualizada.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error en tabla: ' . $e->getMessage();
            }
        }
    }
}

// Cargar fixture y tabla desde MySQL
$partidos = [];
$tabla    = [];
try {
    $partidos = $pdo->query('SELECT * FROM partidos_fixture ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    $tabla    = $pdo->query('SELECT * FROM tabla_posiciones ORDER BY posicion ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error leyendo partidos: ' . $e->getMessage());
}

$titulo_pagina = "Partidos y Tabla - " . $nombre_sitio;
$pagina_actual = "partidos";

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f8f9fa; padding-bottom: 50px;">
    
    <!-- ALERTAS -->
    <?php if (!empty($mensaje_exito)): ?>
        <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
            <div style="background:#e8f5e9; border-left:5px solid #2e7d32; color:#1b5e20; padding:12px 18px; border-radius:6px; font-weight:600;">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div style="max-width: 1200px; margin: 20px auto 0; padding: 0 20px;">
            <div style="background:#ffebee; border-left:5px solid #d32f2f; color:#c62828; padding:12px 18px; border-radius:6px; font-weight:600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN 1: FIXTURE DE PARTIDOS -->
    <section id="partidos" class="content-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:15px;">
            <div>
                <h2 style="margin:0; font-size:1.8rem; color:#023e8a;">Próximos Partidos de <?= htmlspecialchars($nombre_sitio); ?></h2>
                <p style="margin:4px 0 0; color:#555;">Consultá el fixture del Albo en el torneo y no te pierdas ningún partido.</p>
            </div>
            <?php if ($es_administrador): ?>
                <span style="background:#e63946; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.75rem; font-weight:700;">
                    <i class="fa-solid fa-pen"></i> MODO EDICIÓN HABILITADO
                </span>
            <?php endif; ?>
        </div>

        <!-- FORMULARIO PARA AGREGAR PARTIDO (SOLO ADMIN) -->
        <?php if ($es_administrador): ?>
            <div style="background:#fff; border-radius:12px; padding:20px; margin-bottom:25px; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.15rem;">
                    <i class="fa-solid fa-plus-circle"></i> Agregar Partido al Fixture (Admin)
                </h3>
                <form action="partidos.php" method="POST" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)) 130px; gap:12px; align-items:flex-end;">
                    <input type="hidden" name="accion" value="agregar_partido">
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Fecha *</label>
                        <input type="text" name="fecha" placeholder="Ej. 15/10/2026" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Rival *</label>
                        <input type="text" name="rival" placeholder="Ej. San Martín (T)" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Condición *</label>
                        <input type="text" name="condicion" placeholder="Local o Visitante" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.8rem; font-weight:700; color:#555; display:block; margin-bottom:4px;">Horario *</label>
                        <input type="text" name="horario" placeholder="Ej. 17:00" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    </div>
                    <div>
                        <button type="submit" style="width:100%; background:#0077b6; color:#fff; border:none; padding:10px; border-radius:4px; font-weight:700; cursor:pointer;">
                            <i class="fa-solid fa-plus"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- TABLA DE FIXTURE -->
        <div class="table-wrapper" style="background:#fff; border-radius:10px; box-shadow:0 3px 12px rgba(0,0,0,0.06); overflow:hidden;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#0077b6; color:#fff;">
                        <th style="padding:12px 14px;">Fecha</th>
                        <th style="padding:12px 14px;">Rival</th>
                        <th style="padding:12px 14px;">Condición</th>
                        <th style="padding:12px 14px;">Horario</th>
                        <?php if ($es_administrador): ?>
                            <th style="padding:12px 14px; text-align:center;">Acciones Admin</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $p): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px 14px; font-weight:600;"><?= htmlspecialchars($p['fecha']); ?></td>
                            <td style="padding:12px 14px; font-weight:700; color:#023e8a;"><?= htmlspecialchars($p['rival']); ?></td>
                            <td style="padding:12px 14px;"><?= htmlspecialchars($p['condicion']); ?></td>
                            <td style="padding:12px 14px;"><?= htmlspecialchars($p['horario']); ?></td>
                            
                            <?php if ($es_administrador): ?>
                                <td style="padding:8px 14px; text-align:center;">
                                    <div style="display:flex; justify-content:center; gap:6px;">
                                        <!-- Form para eliminar partido -->
                                        <form action="partidos.php" method="POST" onsubmit="return confirm('¿Borrar partido contra <?= htmlspecialchars($p['rival']); ?>?');" style="margin:0;">
                                            <input type="hidden" name="accion" value="eliminar_partido">
                                            <input type="hidden" name="partido_id" value="<?= (int)$p['id']; ?>">
                                            <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section-footer"><hr><p>Partidos | Alentando siempre los colores del Albo</p></div>
    </section>

    <!-- SECCIÓN 2: TABLA DE POSICIONES -->
    <section id="tabla" class="content-section alternate active" style="display:block; padding: 20px 20px 40px; max-width: 1200px; margin: 0 auto;">
        <h2 style="color:#023e8a; font-size:1.8rem; margin-bottom:10px;">Tabla de Posiciones</h2>
        <p style="color:#555; margin-bottom:20px;">Posiciones actualizadas del campeonato oficial.</p>

        <div class="table-wrapper" style="background:#fff; border-radius:10px; box-shadow:0 3px 12px rgba(0,0,0,0.06); overflow:hidden;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#1b263b; color:#fff;">
                        <th style="padding:10px 14px; width:60px;">Pos.</th>
                        <th style="padding:10px 14px;">Equipo</th>
                        <th style="padding:10px 14px; text-align:center;">Pts.</th>
                        <th style="padding:10px 14px; text-align:center;">PJ</th>
                        <?php if ($es_administrador): ?>
                            <th style="padding:10px 14px; text-align:center;">Editar Pts/PJ</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabla as $t): ?>
                        <?php $esGyT = str_contains(strtolower($t['equipo']), 'gimnasia y tiro'); ?>
                        <tr style="border-bottom:1px solid #eee; <?= $esGyT ? 'background:#e0f2fe; font-weight:bold;' : ''; ?>">
                            <td style="padding:10px 14px; font-weight:700;"><?= (int)$t['posicion']; ?></td>
                            <td style="padding:10px 14px;"><?= htmlspecialchars($t['equipo']); ?></td>
                            <td style="padding:10px 14px; text-align:center; font-weight:700;"><?= (int)$t['puntos']; ?></td>
                            <td style="padding:10px 14px; text-align:center;"><?= (int)$t['pj']; ?></td>

                            <?php if ($es_administrador): ?>
                                <td style="padding:6px 14px; text-align:center;">
                                    <form action="partidos.php" method="POST" style="display:inline-flex; align-items:center; gap:4px; margin:0;">
                                        <input type="hidden" name="accion" value="editar_posicion">
                                        <input type="hidden" name="posicion" value="<?= (int)$t['posicion']; ?>">
                                        <input type="hidden" name="equipo" value="<?= htmlspecialchars($t['equipo']); ?>">
                                        <input type="number" name="puntos" value="<?= (int)$t['puntos']; ?>" title="Puntos" style="width:50px; padding:3px; font-size:0.75rem; border:1px solid #ccc; border-radius:3px; text-align:center;">
                                        <input type="number" name="pj" value="<?= (int)$t['pj']; ?>" title="Partidos Jugados" style="width:45px; padding:3px; font-size:0.75rem; border:1px solid #ccc; border-radius:3px; text-align:center;">
                                        <button type="submit" style="background:#2e7d32; color:#fff; border:none; padding:3px 6px; border-radius:3px; font-size:0.75rem; cursor:pointer;">
                                            <i class="fa-solid fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section-footer"><hr><p>Tabla | Resultados y estadísticas deportivas</p></div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
