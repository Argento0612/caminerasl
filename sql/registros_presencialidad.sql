-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2025 a las 17:18:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `c2680096_camsl`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_presencialidad`
--

CREATE TABLE `registros_presencialidad` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario_nombre` varchar(255) NOT NULL DEFAULT 'SIN NOMBRE',
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `lugar_servicio` varchar(255) NOT NULL,
  `personal_guardia` text NOT NULL,
  `modalidad` enum('24 HS','48 HS','OFICINA') NOT NULL,
  `hay_ausentes` tinyint(1) DEFAULT 0,
  `personal_ausente` varchar(255) DEFAULT NULL,
  `motivo_ausencia` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registros_presencialidad`
--

INSERT INTO `registros_presencialidad` (`id`, `usuario_id`, `usuario_nombre`, `fecha`, `hora`, `departamento`, `lugar_servicio`, `personal_guardia`, `modalidad`, `hay_ausentes`, `personal_ausente`, `motivo_ausencia`, `created_at`, `updated_at`) VALUES
(26, 28, 'Daniel', '2025-05-12', '18:51:00', 'DEPARTAMENTO TRANSITO', 'PUESTO SUYUQUE', '', '24 HS', 0, '', '', '2025-05-13 00:38:30', '2025-05-13 00:38:30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `registros_presencialidad`
--
ALTER TABLE `registros_presencialidad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registro_dia` (`usuario_id`,`fecha`),
  ADD KEY `idx_registro_fecha` (`fecha`),
  ADD KEY `idx_registro_departamento` (`departamento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `registros_presencialidad`
--
ALTER TABLE `registros_presencialidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 