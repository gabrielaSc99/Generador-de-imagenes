<?php
/**
 * ===========================================
 * ARCHIVO: controlador_api.php
 * ===========================================
 *
 * Este archivo trae datos de APIs externas para inspirar al usuario.
 * Uso dos APIs:
 *
 * 1. QUOTABLE (https://api.quotable.io)
 *    - Me da frases/citas aleatorias
 *    - Gratis, sin API key
 *    - El usuario puede usar estas frases como inspiracion
 *
 * 2. LOREM PICSUM (https://picsum.photos)
 *    - Me da imagenes aleatorias
 *    - Gratis, sin API key
 *    - Util para mostrar ejemplos de fondos
 *
 * ¿QUE ES UNA API?
 * Una API es como un "camarero" entre mi aplicacion y otro servicio.
 * Yo le pido algo (una frase, una imagen) y me lo trae.
 *
 * ¿COMO FUNCIONA?
 * 1. Construyo una URL especial
 * 2. Hago una peticion HTTP (como visitar una pagina)
 * 3. La API me devuelve datos (generalmente en formato JSON)
 * 4. Proceso esos datos y los muestro al usuario
 */

require_once __DIR__ . '/../configuracion.php';

// ===========================================
// URLS DE LAS APIS
// ===========================================

// API de Quotable para frases aleatorias
// Cuando visito esta URL, me devuelve una frase aleatoria en JSON
define('API_QUOTABLE_URL', 'https://api.quotable.io/random');

// API de Lorem Picsum para imagenes aleatorias
// Esta API devuelve imagenes directamente, no JSON
define('API_PICSUM_URL', 'https://picsum.photos');


/**
 * ===========================================
 * FUNCION: obtener_frase_inspiracion()
 * ===========================================
 *
 * Obtiene una frase aleatoria de la API de Quotable.
 
 * @return array|null - ['frase' => '...', 'autor' => '...'] o null si falla
 
 */
function obtener_frase_inspiracion() {

    // -----------------------------------------
    // PASO 1: Hacer la peticion con cURL
    // -----------------------------------------

    // curl_init() inicia cURL
    $curl = curl_init();

    // Configuro las opciones
    curl_setopt_array($curl, [
        // La URL de la API
        CURLOPT_URL => API_QUOTABLE_URL,

        // Que me devuelva el resultado en vez de mostrarlo
        CURLOPT_RETURNTRANSFER => true,

        // Tiempo maximo de espera (10 segundos es suficiente para texto)
        CURLOPT_TIMEOUT => 10,

        // No verificar SSL (para evitar problemas en desarrollo)
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    // Ejecuto la peticion
    $respuesta = curl_exec($curl);

    // Verifico si hubo error
    $error = curl_error($curl);

    // Cierro cURL
    curl_close($curl);

    // -----------------------------------------
    // PASO 2: Verificar la respuesta
    // -----------------------------------------

    // Si hubo error o la respuesta esta vacia, devuelvo null
    if ($error || empty($respuesta)) {
        return null;
    }

    // -----------------------------------------
    // PASO 3: Procesar el JSON
    // -----------------------------------------

    // json_decode() convierte el texto JSON a un array PHP
    // true = devolver como array asociativo (no como objeto)
    $datos = json_decode($respuesta, true);

    // Verifico que tenga los campos que necesito
    if ($datos && isset($datos['content'])) {
        return [
            'frase' => $datos['content'],
            // Si no hay autor, pongo "Anonimo"
            // El operador ?? devuelve el valor de la izquierda si existe,
            // o el de la derecha si no existe
            'autor' => $datos['author'] ?? 'Anonimo'
        ];
    }

    return null;
}


/**
 * ===========================================
 * FUNCION: obtener_varias_frases()
 * ===========================================
 *
 * Obtiene varias frases llamando a la API multiples veces.
 *
 * @param int $cantidad - Cuantas frases quiero
 * @return array - Lista de frases
 
 */
function obtener_varias_frases($cantidad = 5) {
    $frases = [];

    // Hago $cantidad peticiones a la API
    for ($i = 0; $i < $cantidad; $i++) {
        $frase = obtener_frase_inspiracion();

        // Solo agrego si la peticion fue exitosa
        if ($frase) {
            $frases[] = $frase;
        }
    }

    return $frases;
}


/**
 * ===========================================
 * FUNCION: obtener_imagen_fondo()
 * ===========================================
 *
 * Genera una URL de imagen aleatoria de Lorem Picsum.
 * Esta API no necesita peticion previa - la URL directamente devuelve una imagen.
 *
 * @param int $ancho - Ancho de la imagen
 * @param int $alto - Alto de la imagen
 * @return string - URL de la imagen
 
 */
function obtener_imagen_fondo($ancho = 800, $alto = 600) {
    // La URL de Picsum sigue este formato:
    // https://picsum.photos/ANCHO/ALTO
    //
    // El parametro ?random=NUMERO hace que cada imagen sea diferente
    // time() devuelve un numero diferente cada segundo
    return API_PICSUM_URL . '/' . $ancho . '/' . $alto . '?random=' . time();
}


/**
 * ===========================================
 * FUNCION: obtener_imagenes_fondo()
 * ===========================================
 *
 * Genera varias URLs de imagenes aleatorias.
 *
 * @param int $cantidad - Cuantas imagenes quiero
 * @return array - Lista de URLs de imagenes
 */
function obtener_imagenes_fondo($cantidad = 6) {
    $imagenes = [];

    for ($i = 0; $i < $cantidad; $i++) {
        $imagenes[] = [
            // URL de la imagen grande
            'url' => API_PICSUM_URL . '/800/600?random=' . ($i + time()),
            // URL de la miniatura (mas pequeña, carga mas rapido)
            'miniatura' => API_PICSUM_URL . '/200/150?random=' . ($i + time())
        ];
    }

    return $imagenes;
}


/**
 * ===========================================
 * FUNCION: obtener_ideas_prompts()
 * ===========================================
 *
 * Devuelve una lista de ideas predefinidas para inspirar al usuario.
 * Estas no vienen de una API - las escribo yo directamente.
 *
 * Las organizo por categorias para que sea facil encontrar algo.
 *
 * @return array - Ideas organizadas por categoria
 */
function obtener_ideas_prompts() {
    return [
        // Categoria: Paisajes
        'Paisajes' => [
            'atardecer en la playa con palmeras',
            'montanas nevadas al amanecer',
            'bosque encantado con luces magicas',
            'ciudad futurista de noche',
            'jardin japones en primavera'
        ],

        // Categoria: Animales
        'Animales' => [
            'gato astronauta en el espacio',
            'dragon volando sobre un castillo',
            'lobo aullando a la luna',
            'unicornio en un bosque magico',
            'fenix renaciendo del fuego'
        ],

        // Categoria: Arte
        'Arte' => [
            'retrato estilo Van Gogh',
            'ciudad en estilo cyberpunk',
            'paisaje surrealista de Dali',
            'robot con flores creciendo',
            'galaxia dentro de una botella'
        ],

        // Categoria: Fantasia
        'Fantasia' => [
            'castillo flotando en las nubes',
            'portal a otra dimension',
            'criatura mitica en el oceano',
            'arbol de la vida gigante',
            'ciudad submarina con sirenas'
        ]
    ];
}

?>
