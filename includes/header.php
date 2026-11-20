<?php
/**
 * includes/header.php
 * Plantilla del Encabezado (SSI / SSR) con Indicador Visual de Rol (TP8)
 */

if (!defined('APP_NAME')) {
    require_once dirname(__DIR__) . '/config/env.php';
}

if (session_status() === PHP_SESSION_NONE) {
    require_once dirname(__DIR__) . '/auth_guard.php';
}

if (!isset($titulo_pagina) || empty($titulo_pagina)) {
    $titulo_pagina = ($nombre_sitio ?? 'Gimnasia y Tiro de Salta') . ' | Sitio Oficial del Hincha';
}

$esAdminHeader   = es_admin();
$estaAuthHeader  = usuario_esta_autenticado();
$nombreUserHdr   = htmlspecialchars((string)($_SESSION['usuario_nombre'] ?? ($_SESSION['user']['name'] ?? 'Socio')), ENT_QUOTES, 'UTF-8');
$emailUserHdr    = htmlspecialchars((string)($_SESSION['usuario_email'] ?? ($_SESSION['user']['email'] ?? '')), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($titulo_pagina); ?></title>

    <meta name="description" content="Sitio web oficial del hincha del Club Atlético Gimnasia y Tiro de Salta. Historia, estadio El Gigante del Norte, plantel y noticias del Albo.">
    <meta name="author" content="Franco Guantay">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ========================================================================= -->
<!-- 1. BARRA GLOBAL DE ESTADO Y ROL (INDICADOR DE ADMIN / USUARIO / INVITADO) -->
<!-- ========================================================================= -->
<?php if ($esAdminHeader): ?>
    <div style="background: linear-gradient(90deg, #d90429 0%, #0077b6 100%); color: #fff; padding: 10px 15px; font-size: 0.88rem; box-shadow: 0 3px 10px rgba(0,0,0,0.25); position: sticky; top: 0; z-index: 9999;">
        <div style="max-width: 1250px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: #ffd700; color: #000; padding: 3px 10px; border-radius: 12px; font-weight: 800; font-size: 0.78rem; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-crown"></i> ROL: ADMINISTRADOR
                </span>
                <span>
                    <strong><?= $nombreUserHdr; ?></strong> (<code><?= $emailUserHdr; ?></code>) — <em>Modo Edición Habilitado</em>
                </span>
            </div>
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <a href="index.php#editor-noticia-principal" style="background: rgba(255,255,255,0.25); color: #fff; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 0.78rem; font-weight: 700;">
                    <i class="fa-solid fa-newspaper"></i> Editar Portada
                </a>
                <a href="historia.php#editar-historia" style="background: rgba(255,255,255,0.25); color: #fff; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 0.78rem; font-weight: 700;">
                    <i class="fa-solid fa-landmark"></i> Editar Historia & Galería
                </a>
                <a href="admin_panel.php" style="background: #ffd700; color: #000; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 800;">
                    <i class="fa-solid fa-sliders"></i> Panel de Control
                </a>
                <a href="logout.php" style="background: rgba(0,0,0,0.4); color: #fff; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 0.78rem;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>
    </div>
<?php elseif ($estaAuthHeader): ?>
    <div style="background: #023e8a; color: #fff; padding: 8px 15px; font-size: 0.85rem; position: sticky; top: 0; z-index: 9999;">
        <div style="max-width: 1250px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div>
                <span style="background: #0077b6; color: #fff; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 0.75rem;">
                    <i class="fa-solid fa-user"></i> ROL: USUARIO (Solo Navegación)
                </span>
                <span style="margin-left: 8px;">Conectado como: <strong><?= $nombreUserHdr; ?></strong></span>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="dashboard.php" style="background: rgba(255,255,255,0.2); color: #fff; padding: 3px 10px; border-radius: 6px; text-decoration: none; font-size: 0.78rem;">
                    <i class="fa-solid fa-id-card"></i> Mi Carnet Digital
                </a>
                <a href="logout.php" style="background: #e63946; color: #fff; padding: 3px 10px; border-radius: 6px; text-decoration: none; font-size: 0.78rem;">
                    Salir
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div style="background: #212529; color: #adb5bd; padding: 6px 15px; font-size: 0.8rem; text-align: center;">
        <span><i class="fa-solid fa-eye"></i> Navegando como Invitado (Modo Lectura)</span> | 
        <a href="login.php" style="color: #48cae4; font-weight: 700; text-decoration: underline;">
            Iniciar Sesión como Administrador (fguantay39@gmail.com) o Socio
        </a>
    </div>
<?php endif; ?>

<!-- ========================================================================= -->
<!-- 2. TOP BAR CON RELOJ Y ENTORNO -->
<!-- ========================================================================= -->
<div class="top-bar">
    <p>
        <strong><?= htmlspecialchars($nombre_sitio ?? 'GIMNASIA Y TIRO DE SALTA'); ?></strong> | 
        <span id="clock"></span> | 
        <span class="badge-env" style="background:#023e8a; color:#fff; padding:2px 8px; border-radius:12px; font-size:0.75rem; font-weight:600; text-transform:uppercase;">
            <?= htmlspecialchars($entorno_app ?? 'local'); ?>
        </span>
    </p>
</div>

<!-- ========================================================================= -->
<!-- 3. ENCABEZADO PRINCIPAL -->
<!-- ========================================================================= -->
<header class="main-header">
    <div class="header-container">
        <button class="mobile-menu-btn" id="openMenuBtn" aria-label="Abrir menú de navegación">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a href="index.php" class="brand-logo" aria-label="Ir al inicio">
            <span class="logo-text">GIMNASIA <span class="logo-accent">Y TIRO</span></span>
            <span class="logo-subtext">SALTA - ARGENTINA</span>
        </a>

        <a href="contacto.php" class="cart-btn" aria-label="Contacto">
            <i class="fa-solid fa-envelope"></i>
        </a>

        <button id="darkModeBtn" class="dark-mode-btn" type="button" aria-label="Alternar modo oscuro">
            🌙 Modo oscuro
        </button>

        <?php if ($esAdminHeader): ?>
            <a href="admin_panel.php" class="auth-nav-btn" style="text-decoration:none; background:#ffd700; color:#000; font-weight:700;">
                <i class="fa-solid fa-crown"></i> Panel Admin
            </a>
        <?php elseif ($estaAuthHeader): ?>
            <a href="dashboard.php" class="auth-nav-btn" style="text-decoration:none; background:#0077b6;">
                <i class="fa-solid fa-user-circle"></i> Mi Panel
            </a>
        <?php else: ?>
            <a href="login.php" class="auth-nav-btn" id="openAuthBtn" style="text-decoration:none;">
                <i class="fa-solid fa-right-to-bracket"></i> Ingresar
            </a>
        <?php endif; ?>
    </div>
</header>

<?php
// Inclusión de la barra de navegación lateral modular
require_once __DIR__ . '/nav.php';
?>
