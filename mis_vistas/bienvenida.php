<?php
$titulo_pagina = 'Bienvenida';
require_once __DIR__ . '/plantillas/cabecera.php';
?>

<div class="bienvenida-contenedor">
    <div class="bienvenida-hero">
        <h1>Genera imagenes unicas con Inteligencia Artificial</h1>
        <p class="bienvenida-subtitulo">
            Describe cualquier imagen y nuestra IA la creara para ti en segundos.
            Totalmente gratis.
        </p>
        <?php
        // Compruebo si el usuario ha iniciado sesión o no.
        // La función `esta_autenticado()` viene del controlador de usuarios.
        if (!esta_autenticado()):
        ?>
            <!-- Si el usuario NO ha iniciado sesión, muestro los botones para registrarse o entrar -->
            <div class="bienvenida-acciones">
                <!-- `URL_BASE` es una constante definida en `configuracion.php` para la URL principal -->
                <a href="<?php echo URL_BASE; ?>inicio.php?pagina=registro" class="btn btn-primario btn-grande">Crear cuenta gratis</a>
                <a href="<?php echo URL_BASE; ?>inicio.php?pagina=login" class="btn btn-secundario btn-grande">Ya tengo cuenta</a>
            </div>
        <?php else: ?>
            <!-- Si el usuario SÍ ha iniciado sesión, muestro un botón para ir directamente a generar -->
            <a href="<?php echo URL_BASE; ?>inicio.php?pagina=generar" class="btn btn-primario btn-grande">Generar imagen ahora</a>
        <?php endif; ?>
    </div>

    <!-- Sección para destacar las características principales de la aplicación -->
    <div class="bienvenida-caracteristicas">
        <div class="caracteristica">
            <div class="caracteristica-icono">✨</div>
            <h3>Describe y genera</h3>
            <p>Escribe lo que quieres ver y la IA lo crea</p>
        </div>
        <div class="caracteristica">
            <div class="caracteristica-icono">🖌️</div>
            <h3>Múltiples estilos</h3>
            <p>Anime, realista, pintura, pixel art y más</p>
        </div>
        <div class="caracteristica">
            <div class="caracteristica-icono">📚</div>
            <h3>Guarda tu galería</h3>
            <p>Todas tus imágenes en un solo lugar</p>
        </div>
        <div class="caracteristica">
            <div class="caracteristica-icono">📥</div>
            <h3>Descarga gratis</h3>
            <p>Descarga tus imágenes cuando quieras</p>
        </div>
    </div>

    <!-- Sección para mostrar algunos ejemplos de lo que se puede crear -->
    <div class="bienvenida-ejemplos">
        <h2>Ejemplos de lo que puedes crear</h2>
        <div class="ejemplos-grid">
            <div class="ejemplo-item">
                <!-- Un "prompt" es el texto que describes para generar la imagen -->
                <div class="ejemplo-prompt">"Un gato astronauta flotando en el espacio"</div>
            </div>
            <div class="ejemplo-item">
                <div class="ejemplo-prompt">"Castillo medieval al atardecer, estilo fantasía"</div>
            </div>
            <div class="ejemplo-item">
                <div class="ejemplo-prompt">"Robot amigable en un jardín de flores"</div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluyo el pie de página (la parte de abajo con el copyright, etc.).
require_once __DIR__ . '/plantillas/pie.php';
?>