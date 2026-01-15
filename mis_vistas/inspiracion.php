<?php
$titulo_pagina = 'Inspiracion';

require_once __DIR__ . '/plantillas/cabecera.php';
// Incluyo el controlador de la API, que tiene la lógica para obtener las ideas y frases.
require_once __DIR__ . '/../mis_controladores/controlador_api.php';
// Me aseguro de que el usuario haya iniciado sesión.
requerir_autenticacion();
// Pido al controlador que me dé una lista de ideas de prompts (textos para generar imágenes).
$ideas = obtener_ideas_prompts();
?>
<div class="inspiracion-contenedor">
    <h1>Ideas para tus imagenes</h1>
    <p class="subtitulo">Haz clic en cualquier idea para usarla como prompt</p>

    <?php
    // Recorro las ideas, que vienen agrupadas por categoría (ej: "Personajes", "Paisajes").
    // En cada vuelta del bucle, $categoria será el nombre de la categoría y $prompts será la lista de ideas.
    foreach ($ideas as $categoria => $prompts):
    ?>
        <div class="categoria-ideas">
            <h2><?php echo $categoria; ?></h2>
            <div class="lista-ideas">
                <?php
                // Ahora recorro cada una de las ideas (prompts) dentro de la categoría actual.
                foreach ($prompts as $prompt):
                ?>
                    <!-- Cada idea es un enlace. Si haces clic, te lleva a la página del generador. -->
                    <!-- Fíjate en la URL: le pasa el texto del prompt para que el generador ya lo tenga relleno. -->
                    <!-- `urlencode` prepara el texto para que se pueda enviar de forma segura en una URL. -->
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=generar&prompt=<?php echo urlencode($prompt); ?>"
                       class="idea-item">
                        <?php echo htmlspecialchars($prompt); // Muestro el texto de la idea ?>
                    </a>
                <?php endforeach; // Fin del bucle de prompts ?>
            </div>
        </div>
    <?php endforeach; // Fin del bucle de categorías ?>

    <!-- Sección para mostrar frases de inspiración -->
    <div class="seccion-frases">
        <h2>Frases de inspiracion</h2>
        <p>Estas frases pueden darte ideas para tus creaciones:</p>

        <div class="frases-grid">
            <?php
            // Pido al controlador que me dé 4 frases inspiradoras al azar.
            $frases = obtener_varias_frases(4);
            // Si la función me devolvió frases...
            if (!empty($frases)):
                // Las recorro una por una.
                foreach ($frases as $frase):
            ?>
                <div class="frase-item">
                    <!-- Muestro el texto de la frase y su autor -->
                    <p class="frase-texto">"<?php echo htmlspecialchars($frase['frase']); ?>"</p>
                    <p class="frase-autor">- <?php echo htmlspecialchars($frase['autor']); ?></p>
                </div>
            <?php
                endforeach; 
            else:
            ?>
                <!-- Por si acaso hay un error al cargar la imagen -->
                <p>No se pudieron cargar las frases. Intenta recargar la pagina.</p>
            <?php endif; ?>
        </div>

        <!-- Un botón que simplemente recarga la página para obtener nuevas frases -->
        <button onclick="location.reload()" class="btn btn-secundario">Cargar nuevas frases</button>
    </div>
</div>

<?php
require_once __DIR__ . '/plantillas/pie.php';
?>