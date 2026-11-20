<?php
/**
 * includes/header.php
 * Plantilla del Encabezado (SSI / SSR)
 * Incluye metadatos, título dinámico, estilos y navegación principal.
 * Integración con Sesiones PHP Seguras (TP8).
 */

// Asegurar carga de variables de entorno si aún no fueron cargadas
if (!defined('APP_NAME')) {
    require_once dirname(__DIR__) . '/config/env.php';
}

// Cargar guardián para inicializar sesión segura si no está activa
if (session_status() === PHP_SESSION_NONE) {
    require_once dirname(__DIR__) . '/auth_guard.php';
}

if (!isset($titulo_pagina) || empty($titulo_pagina)) {
    $titulo_pagina = ($nombre_sitio ?? 'Gimnasia y Tiro de Salta') . ' | Sitio Oficial del Hincha';
}

$esta_autenticado = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
$nombre_usuario_sesion = $esta_autenticado ? htmlspecialchars((string)($_SESSION['usuario_nombre'] ?? 'Socio'), ENT_QUOTES, 'UTF-8') : '';
$rol_usuario_sesion    = $esta_autenticado ? strtolower((string)($_SESSION['rol'] ?? 'usuario')) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Renderizado Server-Side (SSR): Título Dinámico -->
    <title><?= htmlspecialchars($titulo_pagina); ?></title>

    <!-- Metadatos y SEO -->
    <meta name="description" content="Sitio web del hincha del <?= htmlspecialchars($nombre_sitio ?? 'Club Atlético Gimnasia y Tiro de Salta'); ?>. Enterate de la historia, partidos, noticias del Albo y el Estadio El Gigante del Norte.">
    <meta name="keywords" content="gimnasia y tiro salta, gigante del norte, partido gimnasia y tiro, el albo, federal a, primera nacional">
    <meta name="author" content="Franco Guantay">
    <meta name="robots" content="index, follow">

    <!-- Hojas de Estilo y Fuentes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
    <p>
        <strong><?= htmlspecialchars($nombre_sitio ?? 'GIMNASIA Y TIRO DE SALTA'); ?></strong> | 
        <span id="clock"></span> | 
        <span class="badge-env" style="background:#023e8a; color:#fff; padding:2px 8px; border-radius:12px; font-size:0.75rem; font-weight:600; text-transform:uppercase;">
            <?= htmlspecialchars($entorno_app ?? 'local'); ?>
        </span>
        <?php if ($esta_autenticado): ?>
            | <span style="color:#d4edda; font-weight:600;">
                <i class="fa-solid fa-circle-user"></i> Conectado: <?= $nombre_usuario_sesion; ?> (<?= strtoupper($rol_usuario_sesion); ?>)
            </span>
        <?php endif; ?>
    </p>
</div>

<header class="main-header">
    <div class="header-container">
        <button class="mobile-menu-btn" id="openMenuBtn" aria-label="Abrir menú de navegación">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a href="index.php" class="brand-logo" aria-label="Ir al inicio">
            <span class="logo-text">GIMNASIA <span class="logo-accent">Y TIRO</span></span>
            <span class="logo-subtext">SALTA - ARGENTINA</span>
        </a>

        <a href="contacto.php" class="cart-btn" aria-label="Contacto por correo electrónico">
            <i class="fa-solid fa-envelope"></i>
        </a>

        <button id="darkModeBtn" class="dark-mode-btn" type="button" aria-label="Alternar modo oscuro">
            🌙 Modo oscuro
        </button>

        <?php if ($esta_autenticado): ?>
            <div style="display:flex; align-items:center; gap:8px;">
                <a href="<?= ($rol_usuario_sesion === 'admin') ? 'admin_panel.php' : 'dashboard.php'; ?>" class="auth-nav-btn" style="text-decoration:none; background:#0077b6;">
                    <i class="fa-solid fa-user-circle"></i> Mi Panel
                </a>
                <a href="logout.php" class="auth-nav-btn" style="text-decoration:none; background:#e63946; padding:8px 12px;" title="Cerrar sesión">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
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
