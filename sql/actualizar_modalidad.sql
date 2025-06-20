-- Actualizar la columna modalidad_trabajo en la tabla registro_presencialidad
ALTER TABLE registro_presencialidad 
MODIFY COLUMN modalidad_trabajo ENUM('24 HS', '48 HS', 'OFICINA') NOT NULL;

-- Actualizar registros existentes si es necesario
UPDATE registro_presencialidad 
SET modalidad_trabajo = '24 HS' 
WHERE modalidad_trabajo = 'Presencial';

UPDATE registro_presencialidad 
SET modalidad_trabajo = '48 HS' 
WHERE modalidad_trabajo = 'Remoto';

UPDATE registro_presencialidad 
SET modalidad_trabajo = 'OFICINA' 
WHERE modalidad_trabajo = 'Híbrido'; 