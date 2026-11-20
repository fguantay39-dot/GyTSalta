<?php
/**
 * admin_panel.php
 * Vista Protegida 2: Panel de Administración Albo (Solo Rol 'admin')
 * Trabajo Práctico N° 8 - Control de Acceso Basado en Roles (RBAC)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/auth_guard.php';

// Control estricto de acceso por rol: solo permitido a rol 'admin'
verificar_rol('admin');

require_once __DIR__ . '/db.php';

// Consultas para alimentar el panel administrativo
$totalUsuarios = 0;
$totalPublicaciones = 0;
$totalMensajes = 0;
$usuariosRegistrados = [];

try {
    $totalUsuarios = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $totalPublicaciones = (int)$pdo->query('SELECT COUNT(*) FROM publicaciones')->fetchColumn();
    $totalMensajes = (int)$pdo->query('SELECT COUNT(*) FROM mensajes_contacto')->fetchColumn();

    $stmt = $pdo->query('
        SELECT id, nombre, email, rol, created_at 
        FROM usuarios 
        ORDER BY id ASC
    ');
    $usuariosRegistrados = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error cargando datos en admin_panel: ' . $e->getMessage());
}

$titulo_pagina = 'Panel de Administración | ' . $nombre_sitio;
$pagina_actual = 'admin_panel';

require_once __DIR__ . '/includes/header.php';
?>

<main style="background: #f4f6f9; padding: 40px 15px; min-height: calc(100vh - 200px);">
    <div style="max-width: 1200px; margin: 0 auto;">

        <!-- Cabecera del Panel de Control -->
        <div style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="background: #e63946; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-lock"></i> Zona Restringida - Nivel Administrador
                </span>
                <h1 style="margin: 10px 0 5px; font-size: 1.8rem;">Panel de Administración Albo</h1>
                <p style="margin: 0; opacity: 0.85; font-size: 0.95rem;">
                    Gestión integral de socios, publicaciones y seguridad del Club Atlético Gimnasia y Tiro.
                </p>
            </div>

            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="dashboard.php" style="background: rgba(255,255,255,0.15); color: #fff; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
                    <i class="fa-solid fa-user"></i> Ver como Socio
                </a>
                <a href="logout.php" style="background: #e63946; color: #fff; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>

        <!-- Tarjetas KPI / Métricas -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
            
            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #0077b6;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Socios Registrados</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #023e8a; margin-top: 4px;"><?= $totalUsuarios; ?></div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #0077b6; font-size: 1.4rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #2e7d32;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Noticias Publicadas</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #2e7d32; margin-top: 4px;"><?= $totalPublicaciones; ?></div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2e7d32; font-size: 1.4rem;">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Mensajes de Contacto</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #d97706; margin-top: 4px;"><?= $totalMensajes; ?></div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 1.4rem;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 22px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #7c3aed;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: #666; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Nivel de Seguridad</div>
                        <div style="font-size: 1.3rem; font-weight: 800; color: #7c3aed; margin-top: 6px;">OWASP ASVS</div>
                    </div>
                    <div style="width: 50px; height: 50px; background: #ede9fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 1.4rem;">
                        <i class="fa-solid fa-shield-virus"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tabla de Usuarios del Sistema (Base de Datos MySQL) -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 12px;">
                <h2 style="margin: 0; font-size: 1.25rem; color: #023e8a;">
                    <i class="fa-solid fa-table-users"></i> Padrón de Usuarios y Roles (Tabla <code>usuarios</code>)
                </h2>
                <span style="font-size: 0.85rem; color: #666;">
                    Total registrados: <strong><?= count($usuariosRegistrados); ?></strong>
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6; text-align: left; color: #495057;">
                            <th style="padding: 12px 14px;">ID</th>
                            <th style="padding: 12px 14px;">Nombre</th>
                            <th style="padding: 12px 14px;">Correo electrónico</th>
                            <th style="padding: 12px 14px;">Rol en el Sistema</th>
                            <th style="padding: 12px 14px;">Fecha Alta</th>
                            <th style="padding: 12px 14px; text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuariosRegistrados)): ?>
                            <tr>
                                <td colspan="6" style="padding: 20px; text-align: center; color: #888;">No hay usuarios registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuariosRegistrados as $u): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px 14px; font-weight: 700; color: #023e8a;">#<?= (int)$u['id']; ?></td>
                                    <td style="padding: 12px 14px; font-weight: 600; color: #212529;">
                                        <?= htmlspecialchars((string)$u['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #495057;">
                                        <?= htmlspecialchars((string)$u['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        <?php if (strtolower((string)$u['rol']) === 'admin'): ?>
                                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-weight: 700; font-size: 0.78rem;">
                                                <i class="fa-solid fa-crown"></i> ADMIN
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #e0f2fe; color: #075985; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.78rem;">
                                                <i class="fa-solid fa-user"></i> USUARIO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #6c757d; font-size: 0.85rem;">
                                        <?= htmlspecialchars(substr((string)($u['created_at'] ?? ''), 0, 10), ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: 12px 14px; text-align: center;">
                                        <span style="color: #2e7d32; font-size: 0.85rem;"><i class="fa-solid fa-circle-check"></i> Activo</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ficha Técnica de Seguridad del TP8 -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); padding: 25px;">
            <h3 style="margin-top: 0; color: #023e8a; font-size: 1.15rem; margin-bottom: 15px;">
                <i class="fa-solid fa-shield-halved"></i> Auditoría Técnica del Módulo de Autenticación (TP8)
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; font-size: 0.88rem; color: #444;">
                <div style="background: #f8f9fa; padding: 14px; border-radius: 8px; border-left: 4px solid #0077b6;">
                    <strong>1. Prevención Session Fixation:</strong><br>
                    Al autenticar credenciales correctas en <code>login.php</code>, se ejecuta <code>session_regenerate_id(true)</code> para emitir un nuevo identificador de sesión.
                </div>
                <div style="background: #f8f9fa; padding: 14px; border-radius: 8px; border-left: 4px solid #2e7d32;">
                    <strong>2. Hashing con BCRYPT:</strong><br>
                    Las contraseñas nunca se guardan en texto plano. Se emplea <code>password_hash($pw, PASSWORD_BCRYPT)</code> con salting automático criptográficamente seguro.
                </div>
                <div style="background: #f8f9fa; padding: 14px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <strong>3. Guardián de Rutas (RBAC):</strong><br>
                    El script <code>auth_guard.php</code> protege esta página evaluando <code>verificar_rol('admin')</code>. Usuarios con rol <code>'usuario'</code> son rebotados a su propio panel.
                </div>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
