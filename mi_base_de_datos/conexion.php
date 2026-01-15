<?php
// conexion.php - Conexion a MySQL con PDO

require_once __DIR__ . '/../configuracion.php';

// Creo la conexion a la base de datos
function obtener_conexion() {
    $dsn = "mysql:host=" . BD_HOST . ";dbname=" . BD_NOMBRE . ";charset=" . BD_CHARSET;

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Lanzar excepciones si hay errores 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Resultados como array asociativo
        PDO::ATTR_EMULATE_PREPARES => false,              // Prepared statements reales
    ];

    try {
        $conexion = new PDO($dsn, BD_USUARIO, BD_CONTRASENA, $opciones);
        return $conexion;
    } catch (PDOException $error) {
        die("Error de conexion: " . $error->getMessage());
    }
    //intenta conectar, si falla muestra el error
}

// Ejecuta el SELECT que devuelve todas las filas
function consultar_todos($sql, $parametros = []) {
    $conexion = obtener_conexion();
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetchAll();
}

// Ejecuta el SELECT que devuelve una sola fila
function consultar_uno($sql, $parametros = []) {
    $conexion = obtener_conexion();
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetch();
}

// INSERT(devuelve id), UPDATE o DELETE(devuelve las filas afectadas)
function ejecutar_consulta($sql, $parametros = []) {
    $conexion = obtener_conexion();
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);

    // Si es INSERT devuelvo el nuevo ID, si no las filas afectadas
    if (stripos($sql, 'INSERT') === 0) {
        return $conexion->lastInsertId();
    }
    return $stmt->rowCount();
}

// Contar registros
function contar_registros($sql, $parametros = []) {
    $conexion = obtener_conexion();
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    return (int) $stmt->fetchColumn();
}
?>
