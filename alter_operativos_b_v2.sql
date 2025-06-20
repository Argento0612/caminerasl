ALTER TABLE `operativos_b` 
CHANGE COLUMN `desde_id` `desde` varchar(100) DEFAULT NULL,
CHANGE COLUMN `hasta_id` `hasta` varchar(100) DEFAULT NULL,
CHANGE COLUMN `lugar_id` lugar VARCHAR(255) NOT NULL; 