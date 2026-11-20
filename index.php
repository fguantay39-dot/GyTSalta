<?php
/**
 * index.php
 * Página de Inicio / Portada Principal
 * Trabajo Práctico N° 4 - Migración a PHP, Modularización (SSI) y Renderizado Server-Side (SSR)
 */

// 1. Carga de Variables de Entorno y Configuración Global (.env)
require_once __DIR__ . '/config/env.php';

// 2. Definición de Variables Dinámicas para Renderizado SSR
$titulo_pagina = "Inicio - " . $nombre_sitio;
$pagina_actual = "inicio";

// 3. Inclusión del Encabezado Común y Navegación (SSI)
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- SECCIÓN HERO / BIENVENIDA DINÁMICA -->
    <section id="inicio" class="hero-banner">
        <div class="hero-content">
            <span class="badge-new">BIENVENIDOS A LA PÁGINA DEL HINCHA</span>
            <a href="index.php">
                <img src="https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png" alt="Escudo oficial del club Gimnasia y Tiro de Salta" style="width: 120px; height: auto; display: block; margin: 25px auto;">
            </a>
            <h1>Bienvenido a <?= htmlspecialchars($nombre_sitio); ?></h1>
            <p>La historia, la pasión y la actualidad del club más grande de Salta.</p>
            <div style="margin-top: 15px;">
                <span class="badge-env" style="background:#0077b6; color:#fff; padding:4px 12px; border-radius:15px; font-size:0.85rem; font-weight:600;">
                    Entorno: <?= htmlspecialchars(strtoupper($entorno_app)); ?>
                </span>
            </div>
        </div>
        <div class="section-footer"></div>
    </section>

    <!-- TARJETAS DE ACCESO RÁPIDO A VISTAS MODULARIZADAS -->
    <section class="content-section" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align:center; margin-bottom: 10px;">Secciones del Sitio</h2>
        <p style="text-align:center; margin-bottom: 30px; color:#555;">Explorá los diferentes módulos del club desarrollados con arquitectura PHP Server-Side Rendering (SSR).</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">📰 Noticias</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Enterate del último partido en San Juan y el próximo duelo ante Chacarita en Salta.</p>
                <a href="noticias.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Noticias &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">👥 Plantel</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Conocé a los arqueros, defensores, mediocampistas, delanteros y cuerpo técnico.</p>
                <a href="plantel.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Plantel &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">⚽ Partidos y Tabla</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Fixture completo y tabla de posiciones actualizada del campeonato.</p>
                <a href="partidos.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Partidos &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">🏛️ Historia y Estadio</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Desde la fundación en 1902 hasta el Gigante del Norte, templo del deporte salteño.</p>
                <a href="historia.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ver Historia &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">📱 Contacto</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Envianos tu mensaje o escribinos al correo oficial configurado en el archivo <code>.env</code>.</p>
                <a href="contacto.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ir a Contacto &rarr;</a>
            </div>

            <div style="background:#fff; border-radius:10px; padding:22px; box-shadow:0 3px 10px rgba(0,0,0,0.08); border-top: 4px solid #0077b6;">
                <h3 style="margin-bottom:8px;">🔐 Comunidad Alba</h3>
                <p style="font-size:0.92rem; color:#666; margin-bottom:15px;">Iniciá sesión o registrate para formar parte de la comunidad oficial del club.</p>
                <a href="acceso.php" class="btn-primary" style="display:inline-block; padding:8px 16px; background:#0077b6; color:#fff; border-radius:6px; text-decoration:none; font-weight:600;">Ingresar / Registro &rarr;</a>
            </div>
        </div>

        <div class="section-footer" style="margin-top: 40px;"><hr><p>Inicio | Portal Principal de Gimnasia y Tiro de Salta</p></div>
    </section>
</main>

<?php
// 4. Inclusión del Pie de Página Modular (SSI)
require_once __DIR__ . '/includes/footer.php';
?>
