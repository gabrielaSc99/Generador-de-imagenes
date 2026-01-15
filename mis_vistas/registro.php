<?php
/**
 * registro.php - Formulario para crear cuenta
 */
// Título para la pestaña del navegador.
$titulo_pagina = 'Crear Cuenta';
// Incluyo la cabecera.
require_once __DIR__ . '/plantillas/cabecera.php';
?>

<div class="formulario-contenedor">
    <h1>Crear Cuenta</h1>
    <!-- El formulario envía los datos a `inicio.php` por POST -->
    <form action="<?php echo URL_BASE; ?>inicio.php" method="POST" class="formulario">
        <input type="hidden" name="accion" value="registro">
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre">
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="tu@email.com">
        </div>
        <div class="campo">
            <label for="contrasena">Contrasena</label>
            <!-- `minlength` es una validación de HTML para asegurar que la contraseña tenga al menos 6 caracteres -->
            <input type="password" id="contrasena" name="contrasena" required placeholder="Minimo 6 caracteres" minlength="6">
        </div>
        <button type="submit" class="btn btn-primario">Crear Cuenta</button>
    </form>

    <!-- Mensaje para los que ya se habian registrado antes -->
    <p class="texto-secundario">
        Ya tienes cuenta? <a href="<?php echo URL_BASE; ?>inicio.php?pagina=login">Inicia sesion</a>
    </p>
</div>

<?php
require_once __DIR__ . '/plantillas/pie.php';
?>