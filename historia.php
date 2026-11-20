<?php
/**
 * historia.php
 * Vista de Historia y Estadio
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

$titulo_pagina = "Historia y Estadio - " . $nombre_sitio;
$pagina_actual = "historia";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="historia" class="content-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h2>Historia de <?= htmlspecialchars($nombre_sitio); ?></h2>
        <p>Fundado el <strong>29 de noviembre de 1902</strong>, el Club Atlético Gimnasia y Tiro es la institución deportiva y social decana de la provincia de Salta y una de las más emblemáticas del norte argentino.</p>

        <article style="margin: 25px 0; line-height: 1.8;">
            <h3>Los Orígenes (1902)</h3>
            <p>
                El club nació bajo el nombre de Club Atlético Salteño por iniciativa de un grupo de jóvenes deportistas entusiastas reunidos en el Gran Hotel de la ciudad de Salta. Con el transcurrir de las décadas, incorporó disciplinas atléticas, tiro deportivo, tenis, natación, rugby y básquet, consolidándose como el club de la familia y de la alta competencia en el NOA.
            </p>

            <h3>Ascensos Históricos a Primera División (1993 y 1997)</h3>
            <p>
                Gimnasia y Tiro escribió páginas doradas en el fútbol grande de la Argentina al ascender en dos oportunidades a la Primera División de la AFA (en las temporadas 1993 y 1997), enfrentando a los gigantes del país en el Gigante del Norte ante tribunas colmadas.
            </p>
        </article>

        <div class="section-footer"><hr><p>Historia | Orgullo, tradición y grandeza salteña</p></div>
    </section>

    <section id="estadio" class="content-section alternate active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 20px auto 40px;">
        <h2>Estadio El Gigante del Norte</h2>
        <p>Ubicado en el corazón de la ciudad de Salta, lindante con el Parque San Martín y a pocas cuadras del centro histórico.</p>

        <div style="text-align:center; margin: 20px 0;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Estadio_El_Gigante_del_Norte.jpg/1200px-Estadio_El_Gigante_del_Norte.jpg" alt="Estadio El Gigante del Norte" style="max-width:100%; height:auto; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.15);">
        </div>

        <ul style="list-style:none; padding:0; display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-top:20px;">
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6;"><strong>Capacidad:</strong> 25.000 espectadores</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6;"><strong>Inauguración:</strong> Década de 1920</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6;"><strong>Dirección:</strong> Vicente López y Virrey Toledo, Salta</li>
            <li style="background:#fff; padding:15px; border-radius:8px; border-left:4px solid #0077b6;"><strong>Superficie:</strong> Césped natural</li>
        </ul>

        <div class="section-footer"><hr><p>El Gigante del Norte | El templo del fútbol salteño</p></div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
