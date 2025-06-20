CREATE TABLE IF NOT EXISTS registro_accidentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personal VARCHAR(100) NOT NULL,
    turno ENUM('mañana', 'tarde', 'noche') NOT NULL,
    actividades TEXT NOT NULL,
    observaciones TEXT,
    incidencias TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 