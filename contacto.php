<?php
/**
 * contacto.php
 * Vista de Contacto Institucional
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

$titulo_pagina = "Contacto - " . $nombre_sitio;
$pagina_actual = "contacto";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="contacto" class="content-section active" style="display:block; padding: 40px 20px; max-width: 900px; margin: 0 auto;">
        <h2>📱 Contacto Oficial</h2>
        <p>Comunicate con la administración del sitio o del club a través de los canales institucionales.</p>

        <div class="contacto-lista" style="margin: 25px 0;">
            <p>
                <i class="fa-brands fa-whatsapp"></i>
                <strong>WhatsApp Hincha:</strong>
                <a href="https://wa.me/543875958019" target="_blank" rel="noopener">387 595 8019</a>
            </p>
            <p>
                <i class="fa-brands fa-instagram"></i>
                <strong>Instagram:</strong>
                <a href="https://www.instagram.com/francoguantay_/" target="_blank" rel="noopener">@francoguantay_</a>
            </p>
            <p>
                <i class="fa-solid fa-envelope"></i>
                <strong>Email de Soporte (.env):</strong>
                <a href="mailto:<?= htmlspecialchars($email_contacto); ?>"><?= htmlspecialchars($email_contacto); ?></a>
            </p>
            <p>
                <i class="fa-solid fa-location-dot"></i>
                <strong>Sede Social y Estadio:</strong>
                Vicente López y Virrey Toledo, Salta Capital, Argentina
            </p>
            <p>
                <i class="fa-solid fa-server"></i>
                <strong>Entorno actual:</strong>
                <span class="badge-env" style="background:#023e8a; color:#fff; padding:2px 8px; border-radius:12px; font-size:0.8rem; text-transform:uppercase;">
                    <?= htmlspecialchars($entorno_app); ?>
                </span>
            </p>
        </div>

        <div class="formulario-contacto">
            <h3>Envianos tu Consulta o Mensaje</h3>
            <form id="formularioContacto">
                <label for="nombre">Nombre y Apellido:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ingresá tu nombre" required>
                <small class="error-msg" id="errorNombre"></small>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" placeholder="vos@correo.com" required>
                <small class="error-msg" id="errorEmail"></small>

                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="5" placeholder="Escribí aquí tu mensaje..." required></textarea>
                <small class="error-msg" id="errorMensaje"></small>

                <button type="submit">Enviar mensaje</button>
                <p id="mensajeExito" style="margin-top:15px; font-weight:bold; color:#2e7d32;"></p>
            </form>
        </div>

        <div class="section-footer">
            <hr>
            <p>Contacto | Estamos para ayudarte</p>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
