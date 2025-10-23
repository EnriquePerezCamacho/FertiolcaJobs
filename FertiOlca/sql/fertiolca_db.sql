-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2025 a las 14:25:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fertiolca_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_maquinas`
--

CREATE TABLE `categorias_maquinas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_maquinas`
--

INSERT INTO `categorias_maquinas` (`id`, `nombre`) VALUES
(1, 'Motosierras'),
(2, 'Desbrozadoras'),
(3, 'Tractores');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuadrillas`
--

CREATE TABLE `cuadrillas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_jefe` int(11) NOT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuadrillas`
--

INSERT INTO `cuadrillas` (`id`, `nombre`, `id_jefe`, `ubicacion`, `fecha_actualizacion`) VALUES
(1, 'Cuadrilla Norte', 2, 'Campo 3', '2025-10-16 10:37:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maquinarias`
--

CREATE TABLE `maquinarias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `estado` enum('disponible','ocupada') NOT NULL DEFAULT 'disponible',
  `id_jefe_ocupando` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `maquinarias`
--

INSERT INTO `maquinarias` (`id`, `nombre`, `id_categoria`, `estado`, `id_jefe_ocupando`) VALUES
(1, 'Motosierra 1', 1, 'disponible', NULL),
(2, 'Motosierra 2', 1, 'disponible', NULL),
(3, 'Desbrozadora 1', 2, 'disponible', NULL),
(4, 'Tractor 1', 3, 'disponible', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_cuadrilla` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `id_cuadrilla`) VALUES
(1, 'Juan Pérez', 1),
(2, 'Luis Gómez', 1),
(3, 'Andrés Díaz', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','jefe') NOT NULL DEFAULT 'jefe',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultima_conexion` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `rol`, `activo`, `ultima_conexion`, `creado_en`) VALUES
(1, 'Administrador', 'admin@fertiolca.com', '$2y$10$zJfQnAEO/C.9NLxf9HjO/OlYspk4GK.ZABsrS/bFwjdhLcswwiqrW', 'admin', 1, NULL, '2025-10-16 10:37:03'),
(2, 'Carlos Ruiz', 'jefe@fertiolca.com', '$2y$10$yKnYkkM0nDWqkU1wq1WHH.Bgh8YX9oTAAzdwYNPBH.C3NlTacvC4O', 'jefe', 1, NULL, '2025-10-16 10:37:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_maquinas`
--
ALTER TABLE `categorias_maquinas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuadrillas`
--
ALTER TABLE `cuadrillas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jefe` (`id_jefe`);

--
-- Indices de la tabla `maquinarias`
--
ALTER TABLE `maquinarias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_jefe_ocupando` (`id_jefe_ocupando`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cuadrilla` (`id_cuadrilla`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_maquinas`
--
ALTER TABLE `categorias_maquinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cuadrillas`
--
ALTER TABLE `cuadrillas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `maquinarias`
--
ALTER TABLE `maquinarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuadrillas`
--
ALTER TABLE `cuadrillas`
  ADD CONSTRAINT `cuadrillas_ibfk_1` FOREIGN KEY (`id_jefe`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `maquinarias`
--
ALTER TABLE `maquinarias`
  ADD CONSTRAINT `maquinarias_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_maquinas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maquinarias_ibfk_2` FOREIGN KEY (`id_jefe_ocupando`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`id_cuadrilla`) REFERENCES `cuadrillas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
