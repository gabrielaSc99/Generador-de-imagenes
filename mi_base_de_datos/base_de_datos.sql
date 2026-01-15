-- base_de_datos.sql

CREATE DATABASE IF NOT EXISTS generador_imagenes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE generador_imagenes;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de imagenes generadas
CREATE TABLE IF NOT EXISTS imagenes_generadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    texto_usado TEXT NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    configuracion_json TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Indices para busquedas rapidas
CREATE INDEX idx_usuario_id ON imagenes_generadas(usuario_id);
CREATE INDEX idx_fecha ON imagenes_generadas(fecha_creacion);
