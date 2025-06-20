-- Crear la tabla lugares_servicio si no existe
CREATE TABLE IF NOT EXISTS `lugares_servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar algunos lugares de servicio por defecto
INSERT IGNORE INTO `lugares_servicio` (`nombre`) VALUES
('PUENTE DERIVADOR'),
('AV. ILLIA'),
('RUTA 3'),
('RUTA 7'),
('RUTA 8'),
('RUTA 146'),
('RUTA 147'),
('AUTOPISTA DE LAS SERRANIAS PUNTANAS'); 