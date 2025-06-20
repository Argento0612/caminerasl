-- Crear tabla de tipos de vehículo
CREATE TABLE IF NOT EXISTS tipos_vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de marcas
CREATE TABLE IF NOT EXISTS marcas_vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de modelos
CREATE TABLE IF NOT EXISTS modelos_vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marca_id) REFERENCES marcas_vehiculo(id),
    UNIQUE KEY unique_modelo_por_marca (marca_id, nombre)
);

-- Insertar algunos tipos de vehículo comunes
INSERT IGNORE INTO tipos_vehiculo (nombre) VALUES 
('Automóvil'),
('Camioneta'),
('Motocicleta'),
('Camión'),
('Ómnibus'),
('Furgón'),
('Acoplado'),
('Semirremolque');

-- Insertar algunas marcas comunes
INSERT IGNORE INTO marcas_vehiculo (nombre) VALUES 
('Ford'),
('Chevrolet'),
('Toyota'),
('Volkswagen'),
('Fiat'),
('Renault'),
('Peugeot'),
('Honda'),
('Mercedes-Benz'),
('BMW'); 