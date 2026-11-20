<?php
/**
 * dashboard.php
 * Vista Protegida 1: Panel de la Comunidad Alba / Perfil de Socio
 * Trabajo Práctico N° 8 - Control de Acceso por Autenticación
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/auth_guard.php';

// Proteger la vista: requiere autenticación activa
verificar_autenticacion();

$usuario_id     = $_SESSION['usuario_id'];
$usuario_nombre = htmlspecialchars((string)($_SESSION['usuario_nombre'] ?? 'Socio'), ENT_QUOTES, 'UTF-8');
$usuario_rol    = htmlspecialchars(strtoupper((string)($_SESSION['rol'] ?? 'usuario')), ENT_QUOTES, 'UTF-8');
$usuario_email  = htmlspecialchars((string)($_SESSION['usuario_email'] ?? ''), ENT_QUOTES, 'UTF-8');

$alerta_error = '';
if (($_GET['error'] ?? '') === 'rol_no_autorizado') {
    $alerta_error = 'Acceso Restringido: Tu cuenta posee rol "USUARIO" y no tiene permisos para acceder al Panel de Administración.';
}

$titulo_pagina = 'Mi Panel de Socio | ' . $nombre_sitio;
$pagina_actual = 'dashboard';

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f4f6f9; padding: 40px 15px; min-height: calc(100vh - 200px);">
    <div style="max-width: 1100px; margin: 0 auto;">

        <!-- Alerta de permiso denegado si intentó ingresar al panel admin -->
        <?php if (!empty($alerta_error)): ?>
            <div style="background: #ffebee; border-left: 5px solid #d32f2f; color: #b71c1c; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-weight: 500;">
                <i class="fa-solid fa-ban"></i> <?= $alerta_error; ?>
            </div>
        <?php endif; ?>

        <!-- Banner de Bienvenida -->
        <div style="background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%); color: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,119,182,0.25); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    Portal del Hincha Autenticado
                </span>
                <h1 style="margin: 10px 0 5px; font-size: 1.8rem;">¡Hola, <?= $usuario_nombre; ?>!</h1>
                <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">
                    Bienvenido a tu panel personal de la Comunidad de <strong>Gimnasia y Tiro de Salta</strong>.
                </p>
            </div>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <span style="background: #fff; color: #023e8a; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem;">
                    <i class="fa-solid fa-user-shield"></i> ROL: <?= $usuario_rol; ?>
                </span>
                <a href="logout.php" style="background: #e63946; color: #fff; padding: 8px 16px; border-radius: 20px; font-weight: 600; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión
                </a>
            </div>
        </div>

        <!-- Banner Exclusivo si el usuario es Administrador -->
        <?php if (es_admin()): ?>
            <div style="background: #fff3cd; border: 1px solid #ffeeba; border-left: 5px solid #ffc107; padding: 18px 22px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <strong style="color: #856404; font-size: 1rem;"><i class="fa-solid fa-crown"></i> Privilegios de Administrador Activos</strong>
                    <p style="margin: 4px 0 0; color: #664d03; font-size: 0.88rem;">Tu usuario posee acceso total a la gestión del sistema y socios del club.</p>
                </div>
                <a href="admin_panel.php" style="background: #0077b6; color: #fff; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                    <i class="fa-solid fa-sliders"></i> Ir al Panel de Administración &rarr;
                </a>
            </div>
        <?php endif; ?>

        <!-- Cuadrícula de Módulos del Panel -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">
            
            <!-- TARJETA 1: Carnet Digital del Socio -->
            <div style="background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 3px 12px rgba(0,0,0,0.06); border-top: 4px solid #0077b6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid #eee; padding-bottom: 12px;">
                    <h3 style="margin: 0; color: #023e8a; font-size: 1.15rem;"><i class="fa-solid fa-id-card"></i> Carnet Digital Albo</h3>
                    <span style="background: #e8f5e9; color: #2e7d32; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 12px;">CUOTA AL DÍA</span>
                </div>

                <!-- Simulación visual del Carnet -->
                <div style="background: linear-gradient(135deg, #023e8a 0%, #0077b6 100%); color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); position: relative; overflow: hidden;">
                    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.08; font-weight: 900;">GYT</div>
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <img src="https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png" alt="Escudo" style="width: 50px; height: auto; background: #fff; border-radius: 50%; padding: 4px;">
                        <div>
                            <div style="font-size: 0.75rem; letter-spacing: 1px; opacity: 0.85;">CLUB ATLÉTICO GIMNASIA Y TIRO</div>
                            <div style="font-size: 1.1rem; font-weight: 700;"><?= $usuario_nombre; ?></div>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.82rem; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px;">
                        <div>
                            <div style="opacity: 0.75;">Socio N°</div>
                            <div style="font-weight: 700; font-size: 0.95rem;">GYT-<?= str_pad((string)$usuario_id, 5, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div>
                            <div style="opacity: 0.75;">Ubicación</div>
                            <div style="font-weight: 700;">El Gigante del Norte</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 15px; font-size: 0.85rem; color: #666;">
                    <strong>Correo registrado:</strong> <?= $usuario_email; ?>
                </div>
            </div>

            <!-- TARJETA 2: Seguridad y Sesión Activa (OWASP) -->
            <div style="background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 3px 12px rgba(0,0,0,0.06); border-top: 4px solid #0077b6;">
                <div style="margin-bottom: 18px; border-bottom: 1px solid #eee; padding-bottom: 12px;">
                    <h3 style="margin: 0; color: #023e8a; font-size: 1.15rem;"><i class="fa-solid fa-shield-halved"></i> Seguridad de la Sesión</h3>
                </div>

                <p style="font-size: 0.88rem; color: #555; line-height: 1.5; margin-bottom: 16px;">
                    Esta vista está protegida por <code>auth_guard.php</code> mediante la verificación de la superglobal <code>$_SESSION['usuario_id']</code>.
                </p>

                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem; display: flex; flex-direction: column; gap: 10px;">
                    <li style="display: flex; align-items: center; gap: 10px; color: #2e7d32;">
                        <i class="fa-solid fa-check-circle"></i>
                        <span><strong>Session Fixation Mitigado:</strong> ID de sesión regenerado en login.</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px; color: #2e7d32;">
                        <i class="fa-solid fa-check-circle"></i>
                        <span><strong>Cookies HttpOnly & SameSite:</strong> Inmune a robo de sesión por script.</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px; color: #2e7d32;">
                        <i class="fa-solid fa-check-circle"></i>
                        <span><strong>Hashing BCRYPT:</strong> Contraseñas con factor de coste 12.</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px; color: #0077b6;">
                        <i class="fa-solid fa-info-circle"></i>
                        <span><strong>ID de Sesión actual:</strong> <code><?= substr(session_id(), 0, 16); ?>...</code></span>
                    </li>
                </ul>

                <div style="margin-top: 20px;">
                    <a href="logout.php" style="display: block; text-align: center; padding: 10px; background: #f8f9fa; border: 1px solid #ccc; color: #333; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.88rem;">
                        <i class="fa-solid fa-power-off" style="color: #e63946;"></i> Finalizar sesión de manera segura
                    </a>
                </div>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
