<?php
/**
 * acceso.php
 * Vista de Acceso / Login y Registro a la Comunidad Alba
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

$titulo_pagina = "Comunidad Alba - Iniciar Sesión | " . $nombre_sitio;
$pagina_actual = "acceso";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="acceso" class="content-section auth-section active" style="display:block; padding: 40px 20px; max-width: 1000px; margin: 0 auto;">
        <span id="login"></span><!-- Ancla para compatibilidad con #login -->
        <div class="auth-layout">
            <div class="auth-intro">
                <span class="auth-kicker">COMUNIDAD ALBA</span>
                <h2>Viví el Albo desde adentro</h2>
                <p>Ingresá a tu cuenta o registrate para ser parte de la comunidad oficial del hincha de <strong><?= htmlspecialchars($nombre_sitio); ?></strong>.</p>
                <div class="auth-club-mark" aria-hidden="true">GYT <span>1902</span></div>
            </div>

            <div class="auth-panel">
                <div class="auth-tabs" role="tablist" aria-label="Acceso a la comunidad">
                    <button type="button" class="auth-tab active" role="tab" aria-selected="true" aria-controls="loginForm" data-auth-mode="login">Ingresar</button>
                    <button type="button" class="auth-tab" role="tab" aria-selected="false" aria-controls="registerForm" data-auth-mode="register">Crear cuenta</button>
                </div>

                <form id="loginForm" class="auth-form">
                    <h3>Bienvenido de nuevo</h3>
                    <label for="loginEmail">Correo electrónico</label>
                    <input id="loginEmail" name="email" type="email" autocomplete="email" placeholder="vos@correo.com" required>

                    <label for="loginPassword">Contraseña</label>
                    <input id="loginPassword" name="password" type="password" autocomplete="current-password" placeholder="Tu contraseña" required>

                    <button type="submit" class="auth-submit">Ingresar</button>
                    <p id="loginFeedback" class="auth-feedback" aria-live="polite"></p>
                </form>

                <form id="registerForm" class="auth-form" hidden>
                    <h3>Sumate a la comunidad</h3>
                    <label for="registerName">Nombre y apellido</label>
                    <input id="registerName" name="name" type="text" autocomplete="name" placeholder="Tu nombre" required>

                    <label for="registerEmail">Correo electrónico</label>
                    <input id="registerEmail" name="email" type="email" autocomplete="email" placeholder="vos@correo.com" required>

                    <label for="registerPassword">Contraseña</label>
                    <input id="registerPassword" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="Mínimo 8 caracteres" required>

                    <label for="confirmPassword">Confirmar contraseña</label>
                    <input id="confirmPassword" name="confirmPassword" type="password" autocomplete="new-password" minlength="8" placeholder="Repetí tu contraseña" required>

                    <button type="submit" class="auth-submit">Crear cuenta</button>
                    <p id="registerFeedback" class="auth-feedback" aria-live="polite"></p>
                </form>

                <p class="auth-notice">Tus credenciales se procesan de forma segura bajo el entorno <strong><?= htmlspecialchars(strtoupper($entorno_app)); ?></strong>.</p>
            </div>
        </div>

        <div class="section-footer">
            <hr>
            <p>Comunidad Alba | Autenticación y registro de hinchas</p>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
