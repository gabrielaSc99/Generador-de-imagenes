<?php
/**
 * ===========================================
 * ARCHIVO: controlador_imagenes.php
 * ===========================================
 *
 * Este archivo se encarga de todo lo relacionado con las imágenes:
 * - Generarlas usando la API externa de Pollinations.ai.
 * - Guardar la información en la base de datos.
 * - Obtener las imágenes de un usuario (galería).
 * - Eliminar imágenes.
 *
 * La API de Pollinations.ai es un servicio gratuito que no requiere
 * una clave (API Key) para su uso básico.
 */

require_once __DIR__ . '/../configuracion.php';
require_once __DIR__ . '/../mi_base_de_datos/conexion.php';
require_once __DIR__ . '/controlador_usuarios.php';

/**
 * URL base de la API de Pollinations.ai para generar imágenes.
 * El prompt se añade al final de esta URL.
 */
define('API_POLLINATIONS_URL', 'https://image.pollinations.ai/prompt/');

/**
 * Tiempo máximo de espera (en segundos) para la respuesta de la API.
 * Si la API tarda más que esto, la petición se cancelará.
 */
define('API_TIMEOUT', 60);

/**
 * Genera una imagen a partir de un texto (prompt) usando una API externa.
 *
 * @param string $prompt La descripción de la imagen a generar.
 * @param string $estilo El estilo artístico a aplicar (ej: 'photorealistic').
 * @param int $usuario_id El ID del usuario que genera la imagen.
 * @return array Un array con el resultado: ['exito' => bool, 'mensaje' => string, ...datos extra].
 */
function generar_imagen_con_api($prompt, $estilo, $usuario_id) {
    global $MENSAJES;

    // --- 1. Validaciones iniciales ---
    $prompt = trim($prompt); // Elimino espacios en blanco al inicio y final.

    if (empty($prompt)) {
        return ['exito' => false, 'mensaje' => 'Escribe una descripción para la imagen.'];
    }
    if (strlen($prompt) > TEXTO_MAXIMO) {
        return ['exito' => false, 'mensaje' => 'La descripción es muy larga.'];
    }

    // Verifico si el usuario ha alcanzado el límite de imágenes guardadas.
    $total = contar_registros("SELECT COUNT(*) FROM imagenes_generadas WHERE usuario_id = :id", ['id' => $usuario_id]);
    if ($total >= MAXIMO_IMAGENES_POR_USUARIO) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_limite']];
    }

    // --- 2. Preparación del prompt y la URL de la API ---
    // Limpio el prompt para evitar problemas de seguridad (XSS).
    $prompt_limpio = htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8');
    $estilo = trim($estilo);

    // Combino el prompt del usuario con el prompt del estilo seleccionado.
    $prompt_completo = !empty($estilo) ? $prompt_limpio . ', ' . $estilo : $prompt_limpio;
    
    // Codifico el prompt para que sea seguro incluirlo en una URL.
    $prompt_codificado = urlencode($prompt_completo);

    // Construyo la URL final para la petición a la API.
    $url_api = API_POLLINATIONS_URL . $prompt_codificado;
    $url_api .= '?width=' . IMAGEN_ANCHO . '&height=' . IMAGEN_ALTO . '&nologo=true';

    // --- 3. Petición a la API con cURL ---
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url_api,                 // La URL a la que hacer la petición.
        CURLOPT_RETURNTRANSFER => true,         // Devuelve el resultado como un string, no lo imprime directamente.
        CURLOPT_FOLLOWLOCATION => true,         // Sigue las redirecciones que la API pueda enviar.
        CURLOPT_TIMEOUT => API_TIMEOUT,         // Tiempo máximo de espera.
        CURLOPT_SSL_VERIFYPEER => false,        // Necesario en algunos entornos locales (XAMPP) para evitar errores de SSL.
        CURLOPT_USERAGENT => 'MiGeneradorImagenes/1.0', // Identifica nuestra aplicación ante la API.
    ]);

    $imagen_binaria = curl_exec($curl); // Ejecuto la petición y guardo la imagen (datos binarios).
    $error_curl = curl_error($curl);     // Obtengo cualquier error de cURL.
    $codigo_http = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Obtengo el código de respuesta HTTP (200 = OK).
    curl_close($curl); // Cierro la conexión cURL.

    // --- 4. Verificación de la respuesta de la API ---
    if ($error_curl) {
        return ['exito' => false, 'mensaje' => 'Error al conectar con la API: ' . $error_curl];
    }
    if ($codigo_http !== 200) {
        // La API respondió, pero con un error (ej: 404, 500).
        return ['exito' => false, 'mensaje' => 'La API devolvió un error. Código: ' . $codigo_http];
    }
    if (empty($imagen_binaria)) {
        return ['exito' => false, 'mensaje' => 'La API no devolvió una imagen. Intenta de nuevo.'];
    }

    // --- 5. Guardado de la imagen en el servidor ---
    // Genero un nombre de archivo único para evitar sobreescrituras.
    $nombre_archivo = 'img_' . $usuario_id . '_' . time() . '_' . uniqid() . '.png';
    $ruta_completa = RUTA_IMAGENES . $nombre_archivo;

    // Si el directorio de imágenes no existe, lo creo.
    if (!is_dir(RUTA_IMAGENES)) {
        mkdir(RUTA_IMAGENES, 0755, true);
    }

    // Guardo los datos binarios de la imagen en un archivo en el servidor.
    $guardado = file_put_contents($ruta_completa, $imagen_binaria);
    if ($guardado === false) {
        return ['exito' => false, 'mensaje' => 'Error: No se pudo guardar el archivo de la imagen.'];
    }

    // --- 6. Guardado de la información en la Base de Datos ---
    // Preparo un JSON con los detalles de la generación para guardarlo.
    $configuracion = json_encode([
        'prompt' => $prompt,
        'estilo' => $estilo,
        'ancho' => IMAGEN_ANCHO,
        'alto' => IMAGEN_ALTO
    ], JSON_UNESCAPED_UNICODE); // Evita que los caracteres especiales (acentos) se escapen.

    // Inserto un nuevo registro en la tabla de imágenes.
    $nuevo_id = ejecutar_consulta(
        "INSERT INTO imagenes_generadas (usuario_id, texto_usado, ruta_archivo, configuracion_json) VALUES (:usuario_id, :texto, :ruta, :config)",
        [
            'usuario_id' => $usuario_id,
            'texto' => $prompt,
            'ruta' => $nombre_archivo,
            'config' => $configuracion
        ]
    );

    // --- 7. Devolución del resultado exitoso ---
    return [
        'exito' => true,
        'mensaje' => '¡Imagen generada correctamente!',
        'ruta' => $nombre_archivo,
        'url' => URL_IMAGENES . $nombre_archivo,
        'id' => $nuevo_id
    ];
}

/**
 * Obtiene todas las imágenes generadas por un usuario específico.
 *
 * @param int $usuario_id El ID del usuario.
 * @return array Una lista de las imágenes encontradas.
 */
function obtener_galeria($usuario_id) {
    $imagenes = consultar_todos(
        "SELECT * FROM imagenes_generadas WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC",
        ['usuario_id' => $usuario_id]
    );

    // Añado la URL completa y la configuración decodificada a cada imagen.
    foreach ($imagenes as $i => $img) {
        $imagenes[$i]['url'] = URL_IMAGENES . $img['ruta_archivo'];
        $imagenes[$i]['config'] = json_decode($img['configuracion_json'], true);
    }

    return $imagenes;
}

/**
 * Elimina una imagen específica, verificando que pertenezca al usuario.
 *
 * @param int $imagen_id El ID de la imagen a eliminar.
 * @param int $usuario_id El ID del usuario que solicita la eliminación.
 * @return array Un array con el resultado de la operación.
 */
function eliminar_imagen($imagen_id, $usuario_id) {
    global $MENSAJES;

    // Primero, busco la imagen en la BD para asegurarme que es del usuario.
    // Esto evita que un usuario pueda borrar imágenes de otro.
    $imagen = consultar_uno(
        "SELECT * FROM imagenes_generadas WHERE id = :id AND usuario_id = :usuario_id",
        ['id' => $imagen_id, 'usuario_id' => $usuario_id]
    );

    if (!$imagen) {
        return ['exito' => false, 'mensaje' => 'La imagen no existe o no tienes permiso para eliminarla.'];
    }

    // Elimino el archivo físico de la imagen del servidor.
    $ruta = RUTA_IMAGENES . $imagen['ruta_archivo'];
    if (file_exists($ruta)) {
        unlink($ruta);
    }

    // Finalmente, elimino el registro de la base de datos.
    ejecutar_consulta("DELETE FROM imagenes_generadas WHERE id = :id", ['id' => $imagen_id]);

    return ['exito' => true, 'mensaje' => $MENSAJES['imagen_eliminada']];
}

/**
 * Obtiene los datos de una imagen específica, si pertenece al usuario.
 *
 * @param int $imagen_id El ID de la imagen.
 * @param int $usuario_id El ID del usuario.
 * @return array|false Los datos de la imagen o `false` si no se encuentra.
 */
function obtener_imagen($imagen_id, $usuario_id) {
    $imagen = consultar_uno(
        "SELECT * FROM imagenes_generadas WHERE id = :id AND usuario_id = :usuario_id",
        ['id' => $imagen_id, 'usuario_id' => $usuario_id]
    );

    if ($imagen) {
        $imagen['url'] = URL_IMAGENES . $imagen['ruta_archivo'];
        $imagen['config'] = json_decode($imagen['configuracion_json'], true);
    }

    return $imagen;
}

/**
 * Cuenta el número total de imágenes que ha generado un usuario.
 *
 * @param int $usuario_id El ID del usuario.
 * @return int El número de imágenes.
 */
function contar_imagenes_usuario($usuario_id) {
    return contar_registros("SELECT COUNT(*) FROM imagenes_generadas WHERE usuario_id = :id", ['id' => $usuario_id]);
}

/**
 * Devuelve una lista de estilos predefinidos para la generación de imágenes.
 * Cada estilo tiene un 'prompt' asociado que se añade a la descripción del usuario.
 *
 * @return array Lista de estilos.
 */
function obtener_estilos_disponibles() {
    return [
        ['id' => 'ninguno', 'nombre' => 'Sin estilo', 'prompt' => ''],
        ['id' => 'realista', 'nombre' => 'Foto Realista', 'prompt' => 'photorealistic, high quality, detailed, 8k'],
        ['id' => 'anime', 'nombre' => 'Anime', 'prompt' => 'anime style, japanese animation, vibrant colors'],
        ['id' => 'pintura', 'nombre' => 'Pintura al Óleo', 'prompt' => 'oil painting, artistic, brush strokes'],
        ['id' => 'acuarela', 'nombre' => 'Acuarela', 'prompt' => 'watercolor painting, soft colors, delicate'],
        ['id' => 'pixel', 'nombre' => 'Pixel Art', 'prompt' => 'pixel art, 16-bit, retro gaming'],
        ['id' => 'comic', 'nombre' => 'Cómic', 'prompt' => 'comic book style, bold lines, illustration'],
        ['id' => 'minimalista', 'nombre' => 'Minimalista', 'prompt' => 'minimalist, simple, clean design'],
        ['id' => '3d', 'nombre' => 'Render 3D', 'prompt' => '3D render, octane, professional lighting'],
        ['id' => 'fantasia', 'nombre' => 'Fantasía', 'prompt' => 'fantasy art, magical, epic, detailed'],
    ];
}
?>
