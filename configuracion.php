<?php
// configuracion.php - Todas las configuraciones de mi app

// Conexion a la base de datos
define('BD_HOST', 'localhost');
define('BD_NOMBRE', 'generador_imagenes');
define('BD_USUARIO', 'root');
define('BD_CONTRASENA', '');
define('BD_CHARSET', 'utf8mb4');

// Rutas del proyecto
define('RUTA_BASE', __DIR__ . '/');
define('RUTA_IMAGENES', __DIR__ . '/mis_imagenes_generadas/');
define('RUTA_FUENTES', __DIR__ . '/mis_fuentes/');
define('URL_BASE', '/GeneradorIMG/');
define('URL_IMAGENES', URL_BASE . 'mis_imagenes_generadas/');

// Tamaño de las imagenes generadas y los límites del texto
define('IMAGEN_ANCHO', 800);
define('IMAGEN_ALTO', 600);
define('TEXTO_MAXIMO', 500);
define('TEXTO_MINIMO', 3);

// Esto es el límite de imágenes que puedes generar por usuario registrado
define('MAXIMO_IMAGENES_POR_USUARIO', 20);

// Las sesion duran 1h
define('NOMBRE_SESION', 'mi_app_imagenes');
define('TIEMPO_SESION', 3600);

// Mensajes para mostrar al usuario (Array)
$MENSAJES = [
    'registro_exitoso'       => 'Cuenta creada! Ya puedes iniciar sesion.',
    'login_exitoso'          => 'Bienvenido!',
    'logout_exitoso'         => 'Sesion cerrada.',
    'imagen_creada'          => 'Imagen generada con exito!',
    'imagen_eliminada'       => 'Imagen eliminada.',
    'error_campos_vacios'    => 'Rellena todos los campos.',
    'error_email_invalido'   => 'El email no es valido.',
    'error_email_existe'     => 'Este email ya esta registrado.',
    'error_contrasena_corta' => 'La contrasena debe tener minimo 6 caracteres.',
    'error_credenciales'     => 'Email o contrasena incorrectos.',
    'error_sesion'           => 'Tienes que iniciar sesion.',
    'error_texto_largo'      => 'El texto es muy largo.',
    'error_texto_vacio'      => 'Escribe algo para generar la imagen.',
    'error_guardar'          => 'No pude guardar la imagen.',
    'error_limite'           => 'Ya tienes 20 imagenes. Borra alguna para continuar.',
    'error_api'              => 'Error al conectar con la API. Intenta de nuevo.',
];

// Comprueba si ya hay una sesion activa y el if evita errores si intentas iniciar una sesion si ya existe
if (session_status() === PHP_SESSION_NONE) {
    session_name(NOMBRE_SESION);
    session_start();
}

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Mostrar errores en desarrollo por pantalla
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
