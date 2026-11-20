<?php
/**
 * includes/footer.php
 * Plantilla del Pie de Página (SSI / SSR)
 * Datos globales dinámicos (.env), scripts JavaScript y cierre HTML.
 */
?>
    <footer class="main-footer">
        <h2>DESDE 1902</h2>
        <p>Pasión, historia y orgullo salteño.</p>
        <hr>
        <p>&copy; <?= date('Y'); ?> - <strong><?= htmlspecialchars($nombre_sitio ?? 'Gimnasia y Tiro de Salta'); ?></strong> | Entorno: <span style="text-transform:uppercase; font-weight:600;"><?= htmlspecialchars($entorno_app ?? 'local'); ?></span></p>
        <p style="margin-top: 5px; font-size: 0.9rem;">
            Contacto oficial: <a href="mailto:<?= htmlspecialchars($email_contacto ?? 'contacto@gimnasiaytiro.com.ar'); ?>" style="color: #64b5f6; text-decoration: underline;"><?= htmlspecialchars($email_contacto ?? 'contacto@gimnasiaytiro.com.ar'); ?></a>
        </p>
    </footer>

    <!-- Scripts JavaScript -->
    <script src="script.js"></script>

    <!-- Visor Modal para Ampliación de Imágenes -->
    <div id="visorImagen" class="visor-imagen">
        <span class="cerrar" onclick="cerrarImagen()">&times;</span>
        <img id="imagenAmpliada" src="" alt="Imagen ampliada">
    </div>

</body>
</html>
