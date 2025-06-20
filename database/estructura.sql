-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS formulario_db;

-- Usar la base de datos
USE formulario_db;

-- Crear tabla departamentos
CREATE TABLE IF NOT EXISTS departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Crear tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    contraseña VARCHAR(255),
    departamento_id INT,
    rol ENUM('admin', 'efectivo') DEFAULT 'efectivo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

-- Crear tabla formulario_1
CREATE TABLE IF NOT EXISTS formulario_1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    dato1 VARCHAR(255),
    dato2 TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Crear tabla formulario_2
CREATE TABLE IF NOT EXISTS formulario_2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    observacion TEXT,
    cantidad INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
); 