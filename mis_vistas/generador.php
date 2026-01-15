<?php
$titulo_pagina = 'Generar Imagen';
require_once __DIR__ . '/plantillas/cabecera.php';
require_once __DIR__ . '/../mis_controladores/controlador_imagenes.php';
requerir_autenticacion();

// Obtengo los datos del usuario actual.
$usuario = obtener_usuario_actual();
// Obtengo los diferentes estilos de imagen que están disponibles (ej: anime, realista...).
$estilos = obtener_estilos_disponibles();
// Cuento cuántas imágenes ha generado ya este usuario para mostrar un límite.
$total_imagenes = contar_imagenes_usuario($usuario['id']);
?>

<div class="generador-contenedor">
    <h1>Generar Imagen con IA</h1>
    <p class="subtitulo">Describe la imagen que quieres y la IA la creara para ti</p>
    <!-- Muestro un contador de imágenes generadas sobre el total permitido -->
    <div class="contador-imagenes">
        Imagenes: <?php echo $total_imagenes; ?> / <?php echo MAXIMO_IMAGENES_POR_USUARIO; ?>
    </div>

    <!-- El formulario que el usuario rellenará para generar la imagen -->
    <!-- El `action` apunta a `inicio.php`, que es el controlador principal que gestiona todo. -->
    <form action="<?php echo URL_BASE; ?>inicio.php" method="POST" class="formulario formulario-generador" id="form-generar">
        <!-- Este campo oculto le dice al controlador principal qué acción queremos hacer: "generar" -->
        <input type="hidden" name="accion" value="generar">

        <?php
        // Si venimos desde la inspiracion se comprobara la URL
        // Si existe, lo uso para rellenar el campo de texto. Si no, lo dejo vacío.
        $prompt_valor = isset($_GET['prompt']) ? htmlspecialchars($_GET['prompt']) : '';
        ?>
        <div class="campo">
            <label for="prompt">Describe tu imagen</label>
            <textarea id="prompt" name="prompt" required placeholder="Ej: un gato astronauta flotando en el espacio con estrellas de fondo" rows="3" maxlength="<?php echo TEXTO_MAXIMO; ?>"><?php echo $prompt_valor; ?></textarea>
            <small>Maximo <?php echo TEXTO_MAXIMO; ?> caracteres</small>
        </div>
        <div class="campo">
            <label for="estilo">Estilo de la imagen</label>
            <!-- `select` es un menú desplegable -->
            <select id="estilo" name="estilo">
                <?php
                // Hago un bucle para crear una opción en el desplegable por cada estilo disponible.
                foreach ($estilos as $est):
                ?>
                    <option value="<?php echo $est['prompt']; ?>"><?php echo $est['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primario btn-grande" id="btn-generar">
            Generar Imagen
        </button>
    </form>

    <!-- Esta es la zona que se muestra cuando la imagen se está generando -->
    <!-- Por defecto está oculta con `style="display: none;"` -->
    <div id="area-cargando" class="area-cargando" style="display: none;">
        <div class="spinner"></div> <!-- Una animación CSS que da vueltas -->
        <p>Generando imagen con IA... esto puede tardar unos segundos</p>
    </div>

    <?php
    // Compruebo si existe la variable `$imagen_generada` y si la generación tuvo éxito.
    // Esta variable la crea el controlador principal (`inicio.php`) después de generar la imagen.
    if (isset($imagen_generada) && $imagen_generada['exito']):
    ?>
        <!-- Si se generó una imagen, muestro esta sección -->
        <div class="imagen-resultado">
            <h2>Tu imagen generada</h2>
            <!-- Muestro la imagen recién creada -->
            <img src="<?php echo $imagen_generada['url']; ?>" alt="Imagen generada">
            <!-- Doy opciones para descargar o ver en la galería -->
            <div class="acciones-imagen">
                <a href="<?php echo $imagen_generada['url']; ?>" download class="btn btn-secundario">Descargar</a>
                <a href="<?php echo URL_BASE; ?>inicio.php?pagina=galeria" class="btn btn-secundario">Ver en galeria</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript  -->
<script>
// Esto escucha el evento "submit" del formulario (cuando el usuario le da a "Generar Imagen").
document.getElementById('form-generar').addEventListener('submit', function() {
    // Cuando empieza a generar, deshabilito el botón para que no se pueda pulsar dos veces.
    document.getElementById('btn-generar').disabled = true;
    document.getElementById('btn-generar').textContent = 'Generando...';
    // Y lo más importante, muestro el área de "cargando" que antes estaba oculta.
    document.getElementById('area-cargando').style.display = 'block';
});
</script>

<?php
require_once __DIR__ . '/plantillas/pie.php';
?>