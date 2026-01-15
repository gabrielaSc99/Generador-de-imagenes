<?php
/**
 * ===========================================
 * ARCHIVO: inicio.php
 * ===========================================
 *
 * Este es el ARCHIVO PRINCIPAL de mi aplicacion.
 * Todas las peticiones pasan por aqui.
 *
 * ¿QUE ES UN ENRUTADOR?
 * Es como un "recepcionista" que decide a donde va cada persona.
 * Segun lo que pida el usuario (login, generar, galeria, etc.)
 * este archivo carga la pagina correcta.
 *
 * ¿COMO FUNCIONA?
 * 1. Recibo la pagina que quiere ver el usuario (?pagina=login)
 * 2. Recibo la accion que quiere hacer (?accion=generar)
 * 3. Proceso la accion si hay una (login, registro, generar, etc.)
 * 4. Cargo la vista correspondiente
 *
 * URLs DE EJEMPLO:
 * - inicio.php                    → Pagina de bienvenida
 * - inicio.php?pagina=login       → Formulario de login
 * - inicio.php?pagina=registro    → Formulario de registro
 * - inicio.php?pagina=generar     → Generador de imagenes
 * - inicio.php?pagina=galeria     → Galeria del usuario
 * - inicio.php?pagina=inspiracion → Ideas para prompts
 *
 * ACCIONES (se procesan antes de mostrar la pagina):
 * - accion=login      → Procesar formulario de login
 * - accion=registro   → Procesar formulario de registro
 * - accion=logout     → Cerrar sesion
 * - accion=generar    → Generar una imagen
 * - accion=eliminar   → Eliminar una imagen
 */

// ===========================================
// INCLUIR ARCHIVOS NECESARIOS
// ===========================================

// Cargo la configuracion (constantes, sesiones, mensajes)
require_once __DIR__ . '/configuracion.php';

// Cargo los controladores (funciones para usuarios e imagenes)
require_once __DIR__ . '/mis_controladores/controlador_usuarios.php';
require_once __DIR__ . '/mis_controladores/controlador_imagenes.php';


// ===========================================
// OBTENER PAGINA Y ACCION DE LA URL
// ===========================================

// $_REQUEST contiene datos de $_GET, $_POST y $_COOKIE
// $_GET son los parametros de la URL: inicio.php?pagina=login → $_GET['pagina'] = 'login'
// $_POST son los datos enviados por formularios

// El operador ?? devuelve el valor de la derecha si la izquierda no existe
// Es decir: si no hay accion en la URL, $accion sera un string vacio ''
$accion = $_REQUEST['accion'] ?? '';

// Si no hay pagina en la URL, muestro 'bienvenida' por defecto
$pagina = $_GET['pagina'] ?? 'bienvenida';


// ===========================================
// PROCESAR ACCIONES (FORMULARIOS)
// ===========================================



// $_SERVER['REQUEST_METHOD'] me dice como se hizo la peticion
// 'POST' = se envio un formulario
// 'GET' = se visito una URL normal

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($accion)) {

    // switch es como un "menu" que ejecuta codigo segun el valor de $accion
    switch ($accion) {

        // -----------------------------------------
        // ACCION: REGISTRO
        // -----------------------------------------
        // Se ejecuta cuando el usuario envia el formulario de registro
        case 'registro':

            // Llamo a la funcion registrar_usuario() del controlador
            // Le paso los datos del formulario ($_POST)
            // ?? '' significa: si no existe, usar un string vacio
            $resultado = registrar_usuario(
                $_POST['nombre'] ?? '',
                $_POST['email'] ?? '',
                $_POST['contrasena'] ?? ''
            );

            // Segun si fue exitoso o no, guardo un mensaje y redirijo
            if ($resultado['exito']) {
                // Guardo el mensaje de exito en la sesion
                // Lo mostrare en la pagina de login
                $_SESSION['mensaje_exito'] = $resultado['mensaje'];

                // header('Location: ...') redirige al navegador a otra URL
                header('Location: ' . URL_BASE . 'inicio.php?pagina=login');

                // exit detiene la ejecucion
                // Si no lo pongo, el codigo seguiria ejecutandose
                exit;
            } else {
                // Hubo un error, guardo el mensaje y vuelvo al registro
                $_SESSION['mensaje_error'] = $resultado['mensaje'];
                header('Location: ' . URL_BASE . 'inicio.php?pagina=registro');
                exit;
            }
            break;  // break sale del switch


        // -----------------------------------------
        // ACCION: LOGIN
        // -----------------------------------------
        // Se ejecuta cuando el usuario envia el formulario de login
        case 'login':

            // Intento iniciar sesion con los datos del formulario
            $resultado = iniciar_sesion(
                $_POST['email'] ?? '',
                $_POST['contrasena'] ?? ''
            );

            if ($resultado['exito']) {
                // Login exitoso, llevo al usuario al generador
                $_SESSION['mensaje_exito'] = $resultado['mensaje'];
                header('Location: ' . URL_BASE . 'inicio.php?pagina=generar');
                exit;
            } else {
                // Login fallido, vuelvo al formulario con error
                $_SESSION['mensaje_error'] = $resultado['mensaje'];
                header('Location: ' . URL_BASE . 'inicio.php?pagina=login');
                exit;
            }
            break;


        // -----------------------------------------
        // ACCION: LOGOUT
        // -----------------------------------------
        // Se ejecuta cuando el usuario hace clic en "Salir"
        case 'logout':

            // Cierro la sesion
            cerrar_sesion();

            // Reinicio la sesion para poder guardar el mensaje
            // (cerrar_sesion() destruye la sesion anterior)
            session_start();

            // Muestro mensaje de confirmacion
            $_SESSION['mensaje_exito'] = 'Sesion cerrada correctamente.';

            // Llevo a la pagina de inicio
            header('Location: ' . URL_BASE . 'inicio.php');
            exit;
            break;


        // -----------------------------------------
        // ACCION: GENERAR IMAGEN
        // -----------------------------------------
        // Se ejecuta cuando el usuario envia el formulario de generacion
        case 'generar':

            // Verifico que el usuario este logueado
            // Si no lo esta, requerir_autenticacion() lo redirige al login
            requerir_autenticacion();

            // Obtengo los datos del usuario actual
            $usuario = obtener_usuario_actual();

            // Llamo a la funcion que genera la imagen con la API
            $resultado = generar_imagen_con_api(
                $_POST['prompt'] ?? '',    // La descripcion de la imagen
                $_POST['estilo'] ?? '',    // El estilo elegido
                $usuario['id']              // El ID del usuario
            );

            if ($resultado['exito']) {
                // Imagen generada correctamente
                $_SESSION['mensaje_exito'] = $resultado['mensaje'];

                // Guardo la informacion de la imagen para mostrarla
                $_SESSION['ultima_imagen'] = $resultado;
            } else {
                // Hubo un error
                $_SESSION['mensaje_error'] = $resultado['mensaje'];
            }

            // Vuelvo al generador para mostrar el resultado
            header('Location: ' . URL_BASE . 'inicio.php?pagina=generar');
            exit;
            break;


        // -----------------------------------------
        // ACCION: ELIMINAR IMAGEN
        // -----------------------------------------
        // Se ejecuta cuando el usuario hace clic en "Eliminar"
        case 'eliminar':

            // Verifico que este logueado
            requerir_autenticacion();

            $usuario = obtener_usuario_actual();

            // Obtengo el ID de la imagen de la URL
            // inicio.php?accion=eliminar&id=5 → $_GET['id'] = 5
            $imagen_id = $_GET['id'] ?? 0;

            // Intento eliminar la imagen
            // La funcion verifica que la imagen sea del usuario
            $resultado = eliminar_imagen($imagen_id, $usuario['id']);

            if ($resultado['exito']) {
                $_SESSION['mensaje_exito'] = $resultado['mensaje'];
            } else {
                $_SESSION['mensaje_error'] = $resultado['mensaje'];
            }

            // Vuelvo a la galeria
            header('Location: ' . URL_BASE . 'inicio.php?pagina=galeria');
            exit;
            break;
    }
}


// ===========================================
// PREPARAR DATOS PARA LAS VISTAS
// ===========================================

// Si hay un prompt en la URL (viene de la pagina de inspiracion)
// lo guardo para mostrarlo en el formulario
if (isset($_GET['prompt'])) {
    $prompt_predefinido = $_GET['prompt'];
}

// Si hay una imagen recien generada, la paso a la vista
// y luego la borro de la sesion para que no se muestre otra vez
if (isset($_SESSION['ultima_imagen'])) {
    $imagen_generada = $_SESSION['ultima_imagen'];
    unset($_SESSION['ultima_imagen']);  // unset() elimina una variable
}


// ===========================================
// MOSTRAR LA PAGINA CORRESPONDIENTE
// ===========================================

// Segun el valor de $pagina, cargo una vista diferente
// require_once incluye el archivo una sola vez

switch ($pagina) {

    case 'login':
        // Formulario de inicio de sesion
        require_once __DIR__ . '/mis_vistas/login.php';
        break;

    case 'registro':
        // Formulario de registro
        require_once __DIR__ . '/mis_vistas/registro.php';
        break;

    case 'generar':
        // Generador de imagenes (requiere login)
        require_once __DIR__ . '/mis_vistas/generador.php';
        break;

    case 'galeria':
        // Galeria del usuario (requiere login)
        require_once __DIR__ . '/mis_vistas/galeria.php';
        break;

    case 'inspiracion':
        // Ideas para prompts (requiere login)
        require_once __DIR__ . '/mis_vistas/inspiracion.php';
        break;

    default:
        // Si la pagina no existe o no se especifico, muestro bienvenida
        require_once __DIR__ . '/mis_vistas/bienvenida.php';
        break;
}

?>
