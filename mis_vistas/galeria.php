<?php
$titulo_pagina = 'Mi Galeria';
require_once __DIR__ . '/plantillas/cabecera.php';
require_once __DIR__ . '/../mis_controladores/controlador_imagenes.php';
requerir_autenticacion();

// Obtengo los datos del usuario que ha iniciado sesión.
$usuario = obtener_usuario_actual();
// Pido al controlador de imágenes que me dé todas las imágenes de este usuario.
$imagenes = obtener_galeria($usuario['id']);
?>

<!-- Contenedor principal de la galería -->
<div class="galeria-contenedor">
    <h1>Mi Galeria</h1>
    <p class="subtitulo">Aqui estan todas tus imagenes generadas</p>

    <?php
    // Compruebo si la variable $imagenes está vacía.
    // Si es así, significa que el usuario aún no ha creado ninguna imagen.
    if (empty($imagenes)):
    ?>
        <!-- Muestro un mensaje indicando que la galería está vacía -->
        <div class="galeria-vacia">
            <p>Todavia no has generado ninguna imagen</p>
            <!-- Y un botón para animarle a crear su primera imagen -->
            <a href="<?php echo URL_BASE; ?>inicio.php?pagina=generar" class="btn btn-primario">Generar mi primera imagen</a>
        </div>
    <?php else: ?>
        <!-- Si hay imágenes, las muestro en una cuadrícula (grid) -->
        <div class="galeria-grid">
            <?php
            // Hago un bucle para recorrer cada una de las imágenes que obtuve de la base de datos.
            // Por cada imagen en el array $imagenes, la meto en la variable $img y ejecuto el código de adentro.
            foreach ($imagenes as $img):
            ?>
                <!-- Cada imagen es un item de la cuadrícula -->
                <div class="galeria-item">
                    <!-- Contenedor para la imagen en sí -->
                    <div class="galeria-imagen">
                        <!-- La etiqueta <img> para mostrar la imagen. La URL la saco de la base de datos. -->
                        <!-- `htmlspecialchars` es por seguridad, para evitar que código malicioso se cuele en el texto alternativo. -->
                        <img src="<?php echo $img['url']; ?>" alt="<?php echo htmlspecialchars($img['texto_usado']); ?>">
                    </div>
                    <!-- Información sobre la imagen -->
                    <div class="galeria-info">
                        <!-- Muestro el texto (prompt) que se usó para generar la imagen -->
                        <p class="galeria-prompt"><?php echo htmlspecialchars($img['texto_usado']); ?></p>
                        <!-- Muestro la fecha de creación, dándole un formato más legible (día/mes/año hora:minuto) -->
                        <p class="galeria-fecha"><?php echo date('d/m/Y H:i', strtotime($img['fecha_creacion'])); ?></p>
                        <?php
                        // Compruebo si se guardó una configuración de estilo para esta imagen.
                        if (isset($img['config']['estilo']) && $img['config']['estilo']):
                        ?>
                            <!-- Si hay un estilo, lo muestro -->
                            <p class="galeria-estilo">Estilo: <?php echo htmlspecialchars($img['config']['estilo']); ?></p>
                        <?php endif; ?>
                    </div>
                    <!-- Botones de acción para cada imagen -->
                    <div class="galeria-acciones">
                        <!-- Botón para descargar la imagen. La URL es la de la propia imagen. -->
                        <a href="<?php echo $img['url']; ?>" download class="btn btn-pequeno">Descargar</a>
                        <!-- Botón para eliminar. Llama a la acción 'eliminar' y pasa el ID de la imagen. -->
                        <a href="<?php echo URL_BASE; ?>inicio.php?accion=eliminar&id=<?php echo $img['id']; ?>"
                           class="btn btn-pequeno btn-peligro"
                           onclick="return confirm('Seguro que quieres eliminar esta imagen?')">
                            Eliminar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif;  ?>
</div>

<?php
require_once __DIR__ . '/plantillas/pie.php';
?>