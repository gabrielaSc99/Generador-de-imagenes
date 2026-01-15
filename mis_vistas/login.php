<?php
// Título para la pestaña del navegador.
$titulo_pagina = 'Iniciar Sesion';
require_once __DIR__ . '/plantillas/cabecera.php';
?>

<div class="formulario-contenedor">
    <h1>Iniciar Sesion</h1>
    <!-- El formulario envía los datos a `inicio.php` usando el método POST -->
    <form action="<?php echo URL_BASE; ?>inicio.php" method="POST" class="formulario">
        <!-- Campo oculto para decirle al controlador principal que la acción a realizar es un 'login' -->
        <input type="hidden" name="accion" value="login">
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="tu@email.com">
        </div>
        <div class="campo">
            <label for="contrasena">Contrasena</label>
            <input type="password" id="contrasena" name="contrasena" required placeholder="Tu contrasena">
        </div>
        <button type="submit" class="btn btn-primario">Entrar</button>
    </form>
    <!-- Esto es un mensaje para los que no están registrados -->
    <p class="texto-secundario">
        No tienes cuenta? <a href="<?php echo URL_BASE; ?>inicio.php?pagina=registro">Registrate aqui</a>
    </p>
</div>

<?php
require_once __DIR__ . '/plantillas/pie.php';
?>