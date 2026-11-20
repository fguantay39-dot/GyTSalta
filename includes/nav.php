<?php
/**
 * includes/nav.php
 * Menú de Navegación Principal (SSI)
 * Lógica SSR: Resaltado dinámico de la página activa mediante $pagina_actual
 * Integración con Estado de Autenticación (TP8)
 */
if (!isset($pagina_actual)) {
    $pagina_actual = 'inicio';
}

$nav_esta_autenticado = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
$nav_es_admin = $nav_esta_autenticado && strtolower((string)($_SESSION['rol'] ?? '')) === 'admin';
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar-menu" id="sidebarMenu">
    <div class="sidebar-header">
        <button class="close-menu-btn" id="closeMenuBtn" aria-label="Cerrar menú">&times;</button>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?= ($pagina_actual === 'inicio') ? 'active' : ''; ?>">
                <a href="index.php" class="menu-link <?= ($pagina_actual === 'inicio') ? 'active bold-link' : ''; ?>">Inicio</a>
            </li>
            <li class="<?= ($pagina_actual === 'noticias') ? 'active' : ''; ?>">
                <a href="noticias.php" class="menu-link <?= ($pagina_actual === 'noticias') ? 'active bold-link' : ''; ?>">Noticias</a>
            </li>
            <li class="<?= ($pagina_actual === 'plantel') ? 'active' : ''; ?>">
                <a href="plantel.php" class="menu-link <?= ($pagina_actual === 'plantel') ? 'active bold-link' : ''; ?>">Plantel</a>
            </li>
            <li class="<?= ($pagina_actual === 'partidos') ? 'active' : ''; ?>">
                <a href="partidos.php" class="menu-link <?= ($pagina_actual === 'partidos') ? 'active bold-link' : ''; ?>">Partidos y Tabla</a>
            </li>
            <li class="<?= ($pagina_actual === 'historia') ? 'active' : ''; ?>">
                <a href="historia.php" class="menu-link <?= ($pagina_actual === 'historia') ? 'active bold-link' : ''; ?>">Historia y Estadio</a>
            </li>
            <li class="<?= ($pagina_actual === 'contacto') ? 'active' : ''; ?>">
                <a href="contacto.php" class="menu-link <?= ($pagina_actual === 'contacto') ? 'active bold-link' : ''; ?>">Contacto</a>
            </li>

            <!-- SECCIÓN COMUNIDAD / AUTENTICACIÓN (TP8) -->
            <li style="border-top: 1px solid rgba(255,255,255,0.15); margin-top: 10px; padding-top: 10px;">
                <span style="display:block; padding: 4px 15px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #a0c4ff; font-weight: 700;">
                    Comunidad Albo (TP8)
                </span>
            </li>

            <?php if ($nav_esta_autenticado): ?>
                <li class="<?= ($pagina_actual === 'dashboard') ? 'active' : ''; ?>">
                    <a href="dashboard.php" class="menu-link <?= ($pagina_actual === 'dashboard') ? 'active bold-link' : ''; ?>">
                        <i class="fa-solid fa-id-card"></i> Mi Panel de Socio
                    </a>
                </li>
                <?php if ($nav_es_admin): ?>
                    <li class="<?= ($pagina_actual === 'admin_panel') ? 'active' : ''; ?>">
                        <a href="admin_panel.php" class="menu-link <?= ($pagina_actual === 'admin_panel') ? 'active bold-link' : ''; ?>" style="color: #ffc107;">
                            <i class="fa-solid fa-crown"></i> Panel Admin
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="logout.php" class="menu-link" style="color: #ff6b6b;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar Sesión
                    </a>
                </li>
            <?php else: ?>
                <li class="<?= ($pagina_actual === 'login') ? 'active' : ''; ?>">
                    <a href="login.php" class="menu-link <?= ($pagina_actual === 'login') ? 'active bold-link' : ''; ?>">
                        <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                    </a>
                </li>
                <li class="<?= ($pagina_actual === 'registro') ? 'active' : ''; ?>">
                    <a href="registro.php" class="menu-link <?= ($pagina_actual === 'registro') ? 'active bold-link' : ''; ?>">
                        <i class="fa-solid fa-user-plus"></i> Registrarse
                    </a>
                </li>
            <?php endif; ?>

            <li style="border-top: 1px solid rgba(255,255,255,0.15); margin-top: 10px; padding-top: 10px;">
                <a href="clase5/index.php" class="menu-link">Clase 5: Formularios</a>
            </li>
        </ul>
    </nav>
</aside>
