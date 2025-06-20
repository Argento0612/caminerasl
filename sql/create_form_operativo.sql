-- Crear la tabla form_operativo
CREATE TABLE IF NOT EXISTS form_operativo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_procedimiento DATE NOT NULL,
    hora_procedimiento TIME NOT NULL,
    lugar_procedimiento VARCHAR(255) NOT NULL,
    causa_procedimiento VARCHAR(255) NOT NULL,
    detalle_procedimiento TEXT NOT NULL,
    dependencia_cargo VARCHAR(255) NOT NULL,
    nombre_usuario VARCHAR(100) NOT NULL,
    departamento_usuario VARCHAR(100) NOT NULL,
    rol_usuario ENUM('efectivo', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT KEY busqueda_procedimiento (lugar_procedimiento, causa_procedimiento, detalle_procedimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear trigger para insertar automáticamente los datos del usuario
DELIMITER //
CREATE TRIGGER before_form_operativo_insert
BEFORE INSERT ON form_operativo
FOR EACH ROW
BEGIN
    -- Obtener datos del usuario desde la tabla users
    SELECT username, departamento, rol 
    INTO @nombre, @depto, @rol
    FROM users 
    WHERE username = NEW.nombre_usuario
    LIMIT 1;

    -- Asignar los valores
    SET NEW.departamento_usuario = IFNULL(@depto, NEW.departamento_usuario);
    SET NEW.rol_usuario = IFNULL(@rol, NEW.rol_usuario);
END;
//
DELIMITER ;

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_fecha_procedimiento ON form_operativo(fecha_procedimiento);
CREATE INDEX idx_lugar_procedimiento ON form_operativo(lugar_procedimiento);
CREATE INDEX idx_nombre_usuario ON form_operativo(nombre_usuario);

-- Comentarios de la tabla y columnas
ALTER TABLE form_operativo
    COMMENT 'Tabla para almacenar los procedimientos operativos';

ALTER TABLE form_operativo
    MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'Identificador único del procedimiento',
    MODIFY COLUMN fecha_procedimiento DATE COMMENT 'Fecha en que se realizó el procedimiento',
    MODIFY COLUMN hora_procedimiento TIME COMMENT 'Hora en que se realizó el procedimiento',
    MODIFY COLUMN lugar_procedimiento VARCHAR(255) COMMENT 'Ubicación donde se realizó el procedimiento',
    MODIFY COLUMN causa_procedimiento VARCHAR(255) COMMENT 'Motivo o causa del procedimiento',
    MODIFY COLUMN detalle_procedimiento TEXT COMMENT 'Descripción detallada del procedimiento',
    MODIFY COLUMN dependencia_cargo VARCHAR(255) COMMENT 'Dependencia a cargo del procedimiento',
    MODIFY COLUMN nombre_usuario VARCHAR(100) COMMENT 'Nombre del usuario que registró el procedimiento',
    MODIFY COLUMN departamento_usuario VARCHAR(100) COMMENT 'Departamento al que pertenece el usuario',
    MODIFY COLUMN rol_usuario ENUM('efectivo', 'admin') COMMENT 'Rol del usuario en el sistema',
    MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro',
    MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de última actualización'; 