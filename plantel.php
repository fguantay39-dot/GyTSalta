<?php
/**
 * plantel.php
 * Vista del Plantel Profesional
 * Modularización con Server Side Includes (SSI) y Renderizado Server-Side (SSR)
 */

require_once __DIR__ . '/config/env.php';

$titulo_pagina = "Plantel Oficial - " . $nombre_sitio;
$pagina_actual = "plantel";

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section id="plantel" class="content-section alternate active" style="display:block; padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
        <h2>Plantel Profesional de <?= htmlspecialchars($nombre_sitio); ?></h2>
        <p>Conocé a los protagonistas que defienden la camiseta del Albo en la temporada actual.</p>
        
        <div class="players-grid">
            <!-- ARQUEROS -->
            <div class="players-category">
                <h3>🧤 Arqueros</h3>
                <div class="players-grid">
                    <div class="player-card">
                        <img src="https://images.fotmob.com/image_resources/playerimages/361668.png" alt="Federico Cosentino">
                        <h4>Federico Cosentino</h4>
                        <p>Arquero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://static.wikia.nocookie.net/argentine-footballers-abroad/images/d/dc/Joaqu%C3%ADn_Papaleo.png/revision/latest?cb=20230419100244" alt="Joaquín Papaleo">
                        <h4>Joaquín Papaleo</h4>
                        <p>Arquero</p>
                    </div>
                </div>
            </div>

            <!-- DEFENSORES -->
            <div class="players-category">
                <h3>🛡️ Defensores</h3>
                <div class="players-grid">
                    <div class="player-card">
                        <img src="https://fmdataba.com/images/p/272077.png" alt="Ivo Chaves">
                        <h4>Ivo Chaves</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://images.fotmob.com/image_resources/playerimages/1286445.png" alt="Juan Galetto">
                        <h4>Juan Galetto</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://cdn.soccerwiki.org/images/player/85452.png" alt="Manuel Guanini">
                        <h4>Manuel Guanini</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/149490-1732812281.JPG?lm=4711" alt="Gonzalo Soto">
                        <h4>Gonzalo Soto</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://cdn.soccerwiki.org/images/player/54868.png" alt="Gabriel Díaz">
                        <h4>Gabriel Díaz</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://images.fotmob.com/image_resources/playerimages/1091012.png" alt="Adolfo Tallura">
                        <h4>Adolfo Tallura</h4>
                        <p>Defensor</p>
                    </div>
                    <div class="player-card">
                        <img src="https://images.fotmob.com/image_resources/playerimages/731400.png" alt="Lautaro Montoya">
                        <h4>Lautaro Montoya</h4>
                        <p>Defensor</p>
                    </div>
                </div>
            </div>

            <!-- MEDIOCAMPISTAS -->
            <div class="players-category">
                <h3>⚙️ Mediocampistas</h3>
                <div class="players-grid">
                    <div class="player-card">
                        <img src="https://cdn.soccerwiki.org/images/player/99707.png" alt="Franco Sivetti">
                        <h4>Franco Sivetti</h4>
                        <p>Mediocampista</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/744806-1721143645.png?lm=1" alt="Tiago Banega">
                        <h4>Tiago Banega</h4>
                        <p>Mediocampista</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/267420-1743018795.jpg?lm=1" alt="Walter Montoya">
                        <h4>Walter Montoya</h4>
                        <p>Mediocampista</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/539160-1651692051.png?lm=4711" alt="Nicolás Rinaldi">
                        <h4>Nicolás Rinaldi</h4>
                        <p>Mediocampista</p>
                    </div>
                    <div class="player-card">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTaPfxSl3MDCm-ZizbXR48iYqyEIZZHadaNCXn8K35Dgg&s=10" alt="Matías Birge">
                        <h4>Matías Birge</h4>
                        <p>Mediocampista</p>
                    </div>
                </div>
            </div>

            <!-- DELANTEROS -->
            <div class="players-category">
                <h3>⚽ Delanteros</h3>
                <div class="players-grid">
                    <div class="player-card">
                        <img src="https://sortitoutsi.b-cdn.net/uploads/face/face_2000096234.png" alt="Juan Rocca">
                        <h4>Juan Rocca</h4>
                        <p>Delantero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://cdn.soccerwiki.org/images/player/69298.png" alt="Jonás Aguirre">
                        <h4>Jonás Aguirre</h4>
                        <p>Delantero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/1172076-1776439112.jpeg?lm=1" alt="Juan Capano">
                        <h4>Juan Capano</h4>
                        <p>Delantero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/340164-1727450827.jpg?lm=1" alt="Nicolás Contin">
                        <h4>Nicolás Contin</h4>
                        <p>Delantero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://img.a.transfermarkt.technology/portrait/big/577743-1776439201.jpeg?lm=1" alt="Lautaro Gordillo">
                        <h4>Lautaro Gordillo</h4>
                        <p>Delantero</p>
                    </div>
                    <div class="player-card">
                        <img src="https://images.fotmob.com/image_resources/playerimages/1464126.png" alt="Fabricio Rojas">
                        <h4>Fabricio Rojas</h4>
                        <p>Delantero</p>
                    </div>
                </div>
            </div>

            <!-- DIRECTOR TÉCNICO -->
            <div class="players-category">
                <h3>👔 Director Técnico</h3>
                <div class="players-grid">
                    <div class="player-card">
                        <img src="https://uscdn.eltribuno.com/042026/1775580012426.jpg?cw=720" alt="Juan Manuel Azconzábal">
                        <h4>Juan Manuel Azconzábal</h4>
                        <p>Director Técnico</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-footer">
            <hr>
            <p>Plantel | La fuerza y pasión de Gimnasia y Tiro de Salta</p>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
