<?php
/**
 * partidos.php
 * Vista de Fixture, Próximos Partidos y Tabla de Posiciones
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

$titulo_pagina = "Partidos y Tabla - " . $nombre_sitio;
$pagina_actual = "partidos";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="partidos" class="content-section active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h2>Próximos Partidos de <?= htmlspecialchars($nombre_sitio); ?></h2>
        <p>Consultá el fixture del Albo en el torneo y no te pierdas ningún partido.</p>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Fecha</th><th>Rival</th><th>Condición</th><th>Horario</th></tr>
                </thead>
                <tbody>
                    <tr><td>28/08/2026</td><td>Chacarita Juniors</td><td>Local (Gigante del Norte)</td><td>22:00</td></tr>
                    <tr><td>05/09/2026</td><td>Güemes (Sgo del Estero)</td><td>Visitante</td><td>16:00</td></tr>
                    <tr><td>13/09/2026</td><td>Tristán Suárez</td><td>Local (Gigante del Norte)</td><td>17:00</td></tr>
                    <tr><td>19/09/2026</td><td>Agropecuario</td><td>Visitante</td><td>15:00</td></tr>
                    <tr><td>27/09/2026</td><td>Atlético Rafaela</td><td>Local (Gigante del Norte)</td><td>17:00</td></tr>
                    <tr><td>04/10/2026</td><td>San Martín (Tucumán)</td><td>Visitante</td><td>17:00</td></tr>
                    <tr><td>11/10/2026</td><td>Gimnasia y Esgrima (Jujuy)</td><td>Visitante</td><td>17:00</td></tr>
                    <tr><td>16/10/2026</td><td>Atlanta</td><td>Local (Gigante del Norte)</td><td>22:00</td></tr>
                    <tr><td>24/10/2026</td><td>Midland</td><td>Visitante</td><td>17:00</td></tr>
                    <tr><td>01/11/2026</td><td>Quilmes</td><td>Local (Gigante del Norte)</td><td>17:00</td></tr>
                </tbody>
            </table>
        </div>
        <div class="section-footer"><hr><p>Partidos | Alentando siempre los colores del Albo</p></div>
    </section>

    <section id="tabla" class="content-section alternate active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 20px auto 40px;">
        <h2>Tabla de Posiciones</h2>
        <p>Posiciones actualizadas del campeonato oficial.</p>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Pos.</th><th>Equipo</th><th>Pts.</th><th>PJ</th></tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Tristán Suárez</td><td>48</td><td>25</td></tr>
                    <tr><td>2</td><td>Gimnasia y Esgrima (Jujuy)</td><td>45</td><td>26</td></tr>
                    <tr><td>3</td><td>Temperley</td><td>43</td><td>26</td></tr>
                    <tr><td>4</td><td>Atlanta</td><td>42</td><td>25</td></tr>
                    <tr><td>5</td><td>Midland</td><td>38</td><td>25</td></tr>
                    <tr><td>6</td><td>Deportivo Maipú</td><td>38</td><td>26</td></tr>
                    <tr style="background:#e0f2fe; font-weight:bold;"><td>7</td><td><?= htmlspecialchars($nombre_sitio); ?></td><td>38</td><td>26</td></tr>
                    <tr><td>8</td><td>Atlético Rafaela</td><td>36</td><td>26</td></tr>
                    <tr><td>9</td><td>San Martín (Tucumán)</td><td>35</td><td>26</td></tr>
                    <tr><td>10</td><td>Quilmes</td><td>32</td><td>26</td></tr>
                    <tr><td>11</td><td>San Martín (San Juan)</td><td>32</td><td>24</td></tr>
                    <tr><td>12</td><td>Colegiales</td><td>31</td><td>25</td></tr>
                    <tr><td>13</td><td>Almagro</td><td>31</td><td>26</td></tr>
                    <tr><td>14</td><td>Nueva Chicago</td><td>29</td><td>25</td></tr>
                    <tr><td>15</td><td>Agropecuario</td><td>29</td><td>26</td></tr>
                    <tr><td>16</td><td>Chacarita Juniors</td><td>27</td><td>26</td></tr>
                    <tr><td>17</td><td>Patronato</td><td>25</td><td>25</td></tr>
                    <tr><td>18</td><td>Güemes (Sgo del Estero)</td><td>22</td><td>26</td></tr>
                </tbody>
            </table>
        </div>
        <div class="section-footer"><hr><p>Tabla | Resultados y estadísticas deportivas</p></div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
