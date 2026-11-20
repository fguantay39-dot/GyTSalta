<?php
/**
 * noticias.php
 * Vista de Noticias del Club
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

// Variables dinámicas para el SSR
$titulo_pagina = "Noticias - " . $nombre_sitio;
$pagina_actual = "noticias";

// Encabezado común y navegación
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="noticias" class="noticias-container target-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h2 class="titulo-seccion">Noticias de <?= htmlspecialchars($nombre_sitio); ?></h2>
        <p class="subtitulo-seccion">Últimas novedades, resultados y actualidad del Albo salteño.</p>

        <div class="grid-noticias">
            <!-- Noticia 1 -->
            <article class="card-noticia">
                <div class="card-imagen">
                    <img src="https://flex-app.tadevel-cdn.com/hostname/informatesalta.com.ar/api/v1/resizer?asset=6a8a053949e2a0881bb41c74&format=jpeg&width=1200&height=675&focuspoint=0.6031746031746031%2C0.5767483428911894&v=e1d860d7e6b2646b001a4b05b18de61d&s=ebea05a9dd07e631d91bf791a5382d65" alt="Gimnasia y Tiro de Salta">
                </div>
                <div class="card-contenido">
                    <span class="etiqueta-noticia">ÚLTIMO PARTIDO</span>
                    <h3>Gimnasia y Tiro cayó en San Juan y perdió su invicto</h3>
                    <p>
                        El Albo perdió ante San Martín de San Juan y puso fin a una racha de 8 partidos sin derrotas. Ahora el equipo buscará recuperarse rápidamente y volver a sumar de a tres en el Gigante del Norte.
                    </p>
                    <div class="resultado-noticia">
                        <strong>San Martín (SJ)</strong>
                        <span>3 - 1</span>
                        <strong>Gimnasia y Tiro</strong>
                    </div>
                    <p class="dato-noticia">
                        🔵⚪ Invicto de 8 partidos finalizado
                    </p>
                </div>
            </article>

            <!-- Noticia 2 -->
            <article class="card-noticia">
                <div class="card-imagen">
                    <img src="https://ascensodelinterior.com.ar/arc/novedades/1776199136_chacarita-gimnasia-y-tiro.jpg" alt="Gimnasia y Tiro vs Chacarita">
                </div>
                <div class="card-contenido">
                    <span class="etiqueta-noticia">PRÓXIMO PARTIDO</span>
                    <h3>El Albo vuelve al Gigante del Norte</h3>
                    <p>
                        Después de la visita a Cuyo, el plantel conducido por Juan Manuel Azconzábal se prepara para recibir a Chacarita Juniors en un duelo clave por la clasificación.
                    </p>
                    <div class="proximo-partido">
                        <div><span>📅</span><strong>Viernes</strong></div>
                        <div><span>🕙</span><strong>22:00 hs</strong></div>
                        <div><span>🏟️</span><strong>Gigante del Norte</strong></div>
                        <div><span>⚽</span><strong>Gimnasia y Tiro vs. Chacarita</strong></div>
                    </div>
                </div>
            </article>
        </div>

        <div class="section-footer">
            <hr>
            <p>Noticias | Información actualizada del Club Atlético Gimnasia y Tiro</p>
        </div>
    </section>
</main>

<?php
// Pie de página modular
require_once __DIR__ . '/includes/footer.php';
?>
