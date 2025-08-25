-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-08-2025 a las 02:59:55
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
-- Base de datos: `promotion_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` int(11) NOT NULL,
  `campaña_id` int(11) NOT NULL,
  `tipo` enum('audio','video','ficha') NOT NULL,
  `idioma` varchar(10) DEFAULT NULL,
  `url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bandejas_campaña`
--

CREATE TABLE `bandejas_campaña` (
  `id` int(11) NOT NULL,
  `id_campaña` int(11) NOT NULL,
  `id_contacto` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `idioma` varchar(50) NOT NULL,
  `fecha` datetime NOT NULL,
  `mensaje` text NOT NULL,
  `replica` tinyint(1) DEFAULT 0,
  `estado` tinyint(4) DEFAULT NULL,
  `inc_audio` tinyint(1) DEFAULT 0,
  `inc_video` tinyint(1) DEFAULT 0,
  `inc_ficha` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campañas`
--

CREATE TABLE `campañas` (
  `id` int(11) NOT NULL,
  `artista` varchar(255) DEFAULT NULL,
  `tipo_de_lanzamiento` varchar(100) DEFAULT NULL,
  `nombre_lanzamiento` varchar(255) DEFAULT NULL,
  `enlace` text NOT NULL,
  `music_genre` enum('Pop','Rock','Jazz','World Music','Classical') NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `activa` tinyint(1) DEFAULT 1,
  `id_email_account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_accounts`
--

CREATE TABLE `email_accounts` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` text NOT NULL,
  `smtp_host` varchar(150) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `imap_host` varchar(150) NOT NULL,
  `imap_port` int(11) NOT NULL,
  `encryption` varchar(10) NOT NULL DEFAULT 'tls'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enviados`
--

CREATE TABLE `enviados` (
  `id` int(11) NOT NULL,
  `campaña_id` int(11) NOT NULL,
  `id_contacto` int(11) DEFAULT NULL,
  `idioma` varchar(10) DEFAULT NULL,
  `nombre_contacto` varchar(255) DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media_contacts`
--

CREATE TABLE `media_contacts` (
  `id` int(11) NOT NULL,
  `email` varchar(254) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `media_type` varchar(50) DEFAULT NULL,
  `music_genre` enum('Pop','Rock','Jazz','World Music','Classical') DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `language` varchar(30) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `test_email` varchar(254) DEFAULT NULL COMMENT 'Correo de prueba',
  `secondary_language` varchar(30) DEFAULT NULL COMMENT 'Idioma alternativo o segundo idioma'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas_plantillas`
--

CREATE TABLE `respuestas_plantillas` (
  `id` int(11) NOT NULL,
  `idioma` varchar(3) NOT NULL,
  `prefijo` varchar(10) NOT NULL,
  `respuesta` text NOT NULL,
  `despedida` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_archivos_campaña` (`campaña_id`);

--
-- Indices de la tabla `bandejas_campaña`
--
ALTER TABLE `bandejas_campaña`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_campaña` (`id_campaña`),
  ADD KEY `id_contacto` (`id_contacto`);

--
-- Indices de la tabla `campañas`
--
ALTER TABLE `campañas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `email_accounts`
--
ALTER TABLE `email_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `enviados`
--
ALTER TABLE `enviados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaña_id` (`campaña_id`);

--
-- Indices de la tabla `media_contacts`
--
ALTER TABLE `media_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuestas_plantillas`
--
ALTER TABLE `respuestas_plantillas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bandejas_campaña`
--
ALTER TABLE `bandejas_campaña`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campañas`
--
ALTER TABLE `campañas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_accounts`
--
ALTER TABLE `email_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `enviados`
--
ALTER TABLE `enviados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `media_contacts`
--
ALTER TABLE `media_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuestas_plantillas`
--
ALTER TABLE `respuestas_plantillas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD CONSTRAINT `fk_archivos_campaña` FOREIGN KEY (`campaña_id`) REFERENCES `campañas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bandejas_campaña`
--
ALTER TABLE `bandejas_campaña`
  ADD CONSTRAINT `bandejas_campaña_ibfk_1` FOREIGN KEY (`id_campaña`) REFERENCES `campañas` (`id`),
  ADD CONSTRAINT `bandejas_campaña_ibfk_2` FOREIGN KEY (`id_contacto`) REFERENCES `media_contacts` (`id`);

--
-- Filtros para la tabla `enviados`
--
ALTER TABLE `enviados`
  ADD CONSTRAINT `fk_enviados_campaña` FOREIGN KEY (`campaña_id`) REFERENCES `campañas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
