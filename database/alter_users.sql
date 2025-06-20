-- Primero añadir la columna departamento
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS departamento VARCHAR(100) NOT NULL AFTER password;

-- Luego añadir los demás campos
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS rol ENUM('admin', 'efectivo') DEFAULT 'efectivo' AFTER departamento,
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER rol,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Añadir índices si no existen
CREATE INDEX IF NOT EXISTS idx_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_departamento ON users(departamento);

-- Actualizar comentarios de la tabla
ALTER TABLE users COMMENT = 'Tabla de usuarios del sistema';

-- Actualizar comentarios de las columnas
ALTER TABLE users 
  MODIFY COLUMN id INT COMMENT 'Identificador único del usuario',
  MODIFY COLUMN username VARCHAR(100) COMMENT 'Nombre completo del usuario',
  MODIFY COLUMN email VARCHAR(100) COMMENT 'Correo electrónico del usuario',
  MODIFY COLUMN password VARCHAR(255) COMMENT 'Contraseña encriptada del usuario',
  MODIFY COLUMN departamento VARCHAR(100) COMMENT 'Departamento al que pertenece el usuario',
  MODIFY COLUMN rol ENUM('admin', 'efectivo') COMMENT 'Rol del usuario en el sistema',
  MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
  MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización del registro'; 