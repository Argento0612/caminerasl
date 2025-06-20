-- Crear tabla de procedimientos
CREATE TABLE IF NOT EXISTS procedimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    lugar VARCHAR(255) NOT NULL,
    causa VARCHAR(255) NOT NULL,
    detalle TEXT NOT NULL,
    dependencia VARCHAR(255) NOT NULL,
    nombre_usuario VARCHAR(100) NOT NULL,
    departamento VARCHAR(100) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar índices para mejorar el rendimiento de las búsquedas
CREATE INDEX idx_fecha ON procedimientos(fecha);
CREATE INDEX idx_nombre_usuario ON procedimientos(nombre_usuario);
CREATE INDEX idx_departamento ON procedimientos(departamento); 