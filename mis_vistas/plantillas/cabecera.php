<?php
/**
 * cabecera.php 
 */

// Incluyo el archivo de configuración, que tiene datos importantes como la URL base.
require_once __DIR__ . '/../../configuracion.php';
// Incluyo el controlador de usuarios para poder saber si el usuario ha iniciado sesión o no.
require_once __DIR__ . '/../../mis_controladores/controlador_usuarios.php';

// Llamo a la función que me devuelve los datos del usuario actual si ha iniciado sesión.
// Si no ha iniciado sesión, esta variable será `null`.
$usuario_actual = obtener_usuario_actual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina ?? 'Generador de Imagenes con IA'; ?></title>
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>mis_estilos/estilos.css">
</head>
<body>
    <header class="cabecera">
        <div class="contenedor">
            <a href="<?php echo URL_BASE; ?>inicio.php" class="logo">
                Generador IA
            </a>
            <nav class="navegacion">
                <?php
                // Compruebo si el usuario ha iniciado sesión.
                if ($usuario_actual):
                ?>
                    <!-- Si SÍ ha iniciado sesión, muestro estos enlaces: -->
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=generar">Generar</a>
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=galeria">Mi Galeria</a>
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=inspiracion">Inspiracion</a>
                    <!-- Muestro un saludo con su nombre. `htmlspecialchars` por seguridad. -->
                    <span class="usuario-nombre">Hola, <?php echo htmlspecialchars($usuario_actual['nombre']); ?></span>
                    <!-- Y un enlace para cerrar sesión -->
                    <a href="<?php echo URL_BASE; ?>inicio.php?accion=logout" class="btn-logout">Salir</a>
                <?php else: ?>
                    <!-- Si NO ha iniciado sesión, muestro estos otros enlaces: -->
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=login">Entrar</a>
                    <a href="<?php echo URL_BASE; ?>inicio.php?pagina=registro" class="btn-registro">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="contenido-principal">
        <div class="contenedor">
            <?php
            // Esta parte es para mostrar mensajes de alerta al usuario.
            // Los mensajes se guardan en la sesión y se muestran solo una vez.

            // Compruebo si hay un mensaje de éxito guardado en la sesión.
            if (isset($_SESSION['mensaje_exito'])): ?>
                <div class="alerta alerta-exito">
                    <?php 
                    // Muestro el mensaje y luego lo borro (`unset`) para que no vuelva a salir si recargas la página.
                    echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                </div>
            <?php endif; ?>

            <?php // Hago lo mismo para los mensajes de error.
            if (isset($_SESSION['mensaje_error'])): ?>
                <div class="alerta alerta-error">
                    <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                </div>
            <?php endif; ?>