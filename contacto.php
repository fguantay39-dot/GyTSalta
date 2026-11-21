<?php
/**
 * contacto.php
 * Vista de Contacto Institucional y Bandeja de Mensajes para el Administrador
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

// PROCESAMIENTO DE MENSAJES (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'enviar_mensaje';

    // 1. ENVÍO DE CONSULTA POR PARTE DEL USUARIO O VISITANTE
    if ($accion === 'enviar_mensaje') {
        $nombre  = trim((string)($_POST['nombre'] ?? ''));
        $email   = trim((string)($_POST['email'] ?? ''));
        $mensaje = trim((string)($_POST['mensaje'] ?? ''));

        if ($nombre === '' || $email === '' || $mensaje === '') {
            $mensaje_error = 'Completá todos los campos obligatorios.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO mensajes_contacto (nombre, email, mensaje) VALUES (?, ?, ?)');
                $stmt->execute([$nombre, $email, $mensaje]);
                $mensaje_exito = '¡Gracias por comunicarte con Gimnasia y Tiro! Tu mensaje fue recibido.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al enviar: ' . $e->getMessage();
            }
        }
    }

    // 2. BORRADO DE MENSAJE POR EL ADMINISTRADOR
    if ($accion === 'eliminar_mensaje' && $es_administrador) {
        $msg_id = (int)($_POST['mensaje_id'] ?? 0);
        if ($msg_id > 0) {
            try {
                $pdo->prepare('DELETE FROM mensajes_contacto WHERE id = ?')->execute([$msg_id]);
                $mensaje_exito = 'Mensaje eliminado de la bandeja.';
            } catch (PDOException $e) {
                $mensaje_error = 'Error al borrar: ' . $e->getMessage();
            }
        }
    }
}

// Cargar mensajes si es admin
$mensajesRecibidos = [];
if ($es_administrador) {
    try {
        $mensajesRecibidos = $pdo->query('SELECT * FROM mensajes_contacto ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$titulo_pagina = "Contacto - " . $nombre_sitio;
$pagina_actual = "contacto";

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f8f9fa; padding-bottom: 50px;">
    <section id="contacto" class="content-section active" style="display:block; padding: 40px 20px; max-width: 950px; margin: 0 auto;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:15px;">
            <div>
                <h2 style="margin:0; font-size:1.8rem; color:#023e8a;">📱 Contacto Oficial</h2>
                <p style="margin:4px 0 0; color:#555;">Comunicate con la administración del club a través de los canales oficiales.</p>
            </div>
            <?php if ($es_administrador): ?>
                <span style="background:#e63946; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.75rem; font-weight:700;">
                    <i class="fa-solid fa-pen"></i> MODO EDICIÓN HABILITADO
                </span>
            <?php endif; ?>
        </div>

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

        <!-- BANDEJA DE MENSAJES (SOLO VISIBLE PARA ADMIN) -->
        <?php if ($es_administrador): ?>
            <div style="background:#fff; border-radius:12px; padding:22px; margin-bottom:30px; box-shadow:0 3px 12px rgba(0,0,0,0.06); border-top:4px solid #1b263b;">
                <h3 style="margin-top:0; color:#023e8a; font-size:1.2rem;">
                    <i class="fa-solid fa-inbox"></i> Bandeja de Entrada de Mensajes (Admin)
                </h3>
                <p style="color:#666; font-size:0.85rem; margin-bottom:15px;">Mensajes enviados por socios e hinchas desde el formulario web.</p>
                
                <?php if (empty($mensajesRecibidos)): ?>
                    <p style="color:#888; font-size:0.9rem;">No hay mensajes nuevos en la bandeja.</p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php foreach ($mensajesRecibidos as $m): ?>
                            <div style="border:1px solid #eee; border-left:4px solid #0077b6; border-radius:6px; padding:12px 16px; background:#fafafa; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                                <div>
                                    <strong style="color:#023e8a; font-size:0.95rem;"><?= htmlspecialchars($m['nombre']); ?></strong> 
                                    <span style="color:#777; font-size:0.82rem;">(<?= htmlspecialchars($m['email']); ?>)</span>
                                    <span style="font-size:0.75rem; color:#aaa; margin-left:8px;"><?= substr((string)$m['created_at'], 0, 10); ?></span>
                                    <p style="margin:6px 0 0; color:#444; font-size:0.9rem;"><?= nl2br(htmlspecialchars($m['mensaje'])); ?></p>
                                </div>
                                <form action="contacto.php" method="POST" onsubmit="return confirm('¿Borrar este mensaje?');" style="margin:0;">
                                    <input type="hidden" name="accion" value="eliminar_mensaje">
                                    <input type="hidden" name="mensaje_id" value="<?= (int)$m['id']; ?>">
                                    <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:700; cursor:pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- DATOS INSTITUCIONALES -->
        <div class="contacto-lista" style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 3px 10px rgba(0,0,0,0.06); margin-bottom:25px;">
            <p style="margin:8px 0;"><i class="fa-brands fa-whatsapp" style="color:#25d366; width:20px;"></i> <strong>WhatsApp Hincha:</strong> <a href="https://wa.me/543875958019" target="_blank">387 595 8019</a></p>
            <p style="margin:8px 0;"><i class="fa-brands fa-instagram" style="color:#e1306c; width:20px;"></i> <strong>Instagram:</strong> <a href="https://www.instagram.com/francoguantay_/" target="_blank">@francoguantay_</a></p>
            <p style="margin:8px 0;"><i class="fa-solid fa-envelope" style="color:#0077b6; width:20px;"></i> <strong>Email de Soporte (.env):</strong> <a href="mailto:<?= htmlspecialchars($email_contacto); ?>"><?= htmlspecialchars($email_contacto); ?></a></p>
            <p style="margin:8px 0;"><i class="fa-solid fa-location-dot" style="color:#d90429; width:20px;"></i> <strong>Sede Social y Estadio:</strong> Vicente López y Virrey Toledo, Salta Capital, Argentina</p>
        </div>

        <!-- FORMULARIO DE CONTACTO -->
        <div class="formulario-contacto" style="background:#fff; border-radius:10px; padding:25px; box-shadow:0 3px 10px rgba(0,0,0,0.06); border-top:4px solid #0077b6;">
            <h3 style="margin-top:0; color:#023e8a; font-size:1.25rem;">Envianos tu Consulta o Mensaje</h3>
            <form action="contacto.php" method="POST" style="display:flex; flex-direction:column; gap:14px;">
                <input type="hidden" name="accion" value="enviar_mensaje">
                <div>
                    <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Nombre y Apellido:</label>
                    <input type="text" name="nombre" placeholder="Ingresá tu nombre" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Correo electrónico:</label>
                    <input type="email" name="email" placeholder="vos@correo.com" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-weight:600; font-size:0.85rem; margin-bottom:5px;">Mensaje:</label>
                    <textarea name="mensaje" rows="4" placeholder="Escribí aquí tu mensaje..." required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; font-family:inherit;"></textarea>
                </div>
                <div>
                    <button type="submit" style="background:#0077b6; color:#fff; border:none; padding:12px 24px; border-radius:6px; font-weight:700; cursor:pointer; font-size:0.95rem;">
                        <i class="fa-solid fa-paper-plane"></i> Enviar Mensaje
                    </button>
                </div>
            </form>
        </div>

        <div class="section-footer" style="margin-top:35px;">
            <hr>
            <p>Contacto | Estamos para ayudarte</p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
