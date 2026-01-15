<?php
/**
 * ===========================================
 * ARCHIVO: controlador_usuarios.php
 * ===========================================
 *
 * Este archivo gestiona todo lo relacionado con los usuarios:
 * - Registro de nuevas cuentas.
 * - Inicio y cierre de sesión.
 * - Verificación de la autenticación.
 * - Manejo de los datos del usuario en la sesión.
 */

require_once __DIR__ . '/../configuracion.php';
require_once __DIR__ . '/../mi_base_de_datos/conexion.php';

/**
 * Registra un nuevo usuario en la base de datos.
 *
 * @param string $nombre El nombre del usuario.
 * @param string $email El email del usuario.
 * @param string $contrasena La contraseña en texto plano.
 * @return array Un array con el resultado: ['exito' => bool, 'mensaje' => string].
 */
function registrar_usuario($nombre, $email, $contrasena) {
    global $MENSAJES;

    // --- 1. Limpieza y validación de datos ---
    // Se utiliza trim() para quitar espacios y htmlspecialchars() para evitar inyecciones XSS.
    $nombre = trim(htmlspecialchars($nombre));
    $email = trim(htmlspecialchars($email));
    $contrasena = trim($contrasena);

    // Verifico que ningún campo esté vacío.
    if (empty($nombre) || empty($email) || empty($contrasena)) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_campos_vacios']];
    }

    // Valido que el formato del email sea correcto.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_email_invalido']];
    }

    // Impongo una longitud mínima para la contraseña.
    if (strlen($contrasena) < 6) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_contrasena_corta']];
    }

    // --- 2. Verificación en la Base de Datos ---
    // Compruebo si ya existe un usuario con ese email para evitar duplicados.
    $existe = consultar_uno("SELECT id FROM usuarios WHERE email = :email", ['email' => $email]);
    if ($existe) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_email_existe']];
    }

    // --- 3. Creación del usuario ---
    // ¡IMPORTANTE! Nunca guardar contraseñas en texto plano.
    // password_hash() crea un hash seguro y único para la contraseña.
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Inserto el nuevo usuario en la base de datos.
    $nuevo_id = ejecutar_consulta(
        "INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)",
        ['nombre' => $nombre, 'email' => $email, 'contrasena' => $contrasena_hash]
    );

    if ($nuevo_id) {
        return ['exito' => true, 'mensaje' => $MENSAJES['registro_exitoso'], 'usuario_id' => $nuevo_id];
    }
    return ['exito' => false, 'mensaje' => 'Error al crear el usuario.'];
}

/**
 * Inicia la sesión de un usuario si las credenciales son correctas.
 *
 * @param string $email El email del usuario.
 * @param string $contrasena La contraseña en texto plano.
 * @return array Un array con el resultado: ['exito' => bool, 'mensaje' => string].
 */
function iniciar_sesion($email, $contrasena) {
    global $MENSAJES;

    // --- 1. Limpieza y validación de datos ---
    $email = trim(htmlspecialchars($email));
    $contrasena = trim($contrasena);

    if (empty($email) || empty($contrasena)) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_campos_vacios']];
    }

    // --- 2. Búsqueda del usuario ---
    // Busco en la base de datos un usuario con el email proporcionado.
    $usuario = consultar_uno("SELECT * FROM usuarios WHERE email = :email", ['email' => $email]);
    if (!$usuario) {
        // Si el usuario no existe, devuelvo un error genérico para no dar pistas
        // sobre si el email existe o no (mejora la seguridad).
        return ['exito' => false, 'mensaje' => $MENSAJES['error_credenciales']];
    }

    // --- 3. Verificación de la contraseña ---
    // Compruebo si la contraseña proporcionada coincide con el hash guardado.
    // password_verify() es la función segura para esta comprobación.
    if (!password_verify($contrasena, $usuario['contrasena'])) {
        return ['exito' => false, 'mensaje' => $MENSAJES['error_credenciales']];
    }

    // --- 4. Establecimiento de la sesión ---
    // Si las credenciales son correctas, guardo los datos del usuario en la sesión.
    // Esto permite que el usuario permanezca "logueado" mientras navega.
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['sesion_iniciada'] = true;

    return ['exito' => true, 'mensaje' => $MENSAJES['login_exitoso']];
}

/**
 * Cierra la sesión del usuario actual.
 * Destruye toda la información de la sesión.
 */
function cerrar_sesion() {
    global $MENSAJES;
    // Vacío el array de sesión.
    $_SESSION = [];

    // Si se usan cookies de sesión, las elimino.
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Finalmente, destruyo la sesión.
    session_destroy();

    return ['exito' => true, 'mensaje' => $MENSAJES['logout_exitoso']];
}

/**
 * Verifica si hay un usuario autenticado en la sesión actual.
 *
 * @return bool True si el usuario está logueado, false en caso contrario.
 */
function esta_autenticado() {
    return isset($_SESSION['sesion_iniciada']) && $_SESSION['sesion_iniciada'] === true;
}

/**
 * Obtiene los datos del usuario actualmente logueado desde la sesión.
 *
 * @return array|null Un array con los datos del usuario o null si no está logueado.
 */
function obtener_usuario_actual() {
    if (!esta_autenticado()) {
        return null;
    }
    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'email' => $_SESSION['usuario_email']
    ];
}

/**
 * Función de guarda: si el usuario no está autenticado, lo redirige a la página de login.
 * Se debe usar al principio de cualquier página que requiera que el usuario esté logueado.
 */
function requerir_autenticacion() {
    if (!esta_autenticado()) {
        // Guardo un mensaje de error para mostrarlo en la página de login.
        $_SESSION['mensaje_error'] = 'Tienes que iniciar sesión para acceder a esta página.';
        
        // Redirijo al usuario.
        header('Location: ' . URL_BASE . 'inicio.php?pagina=login');
        
        // Es crucial usar exit() después de una redirección para detener la ejecución del script.
        exit;
    }
}
?>
