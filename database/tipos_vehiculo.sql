-- Crear tabla de tipos de vehículo si no existe
CREATE TABLE IF NOT EXISTS tipos_vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar algunos tipos de vehículo comunes si la tabla está vacía
INSERT IGNORE INTO tipos_vehiculo (nombre) VALUES 
('Automóvil'),
('Camioneta'),
('Motocicleta'),
('Camión'),
('Ómnibus'),
('Furgón'),
('Acoplado'),
('Semirremolque'); 