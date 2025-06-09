-- Estructura de la base de datos: promotion_system

-- Tabla: campañas
CREATE TABLE `campañas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo_de_lanzamiento` varchar(100) DEFAULT NULL,
  `enlace` text NOT NULL,
  `music_genre` enum('Pop','Rock','Jazz','World Music','Classical') NOT NULL,
  `fecha_creación` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: enviados
CREATE TABLE `enviados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaña_id` int(11) NOT NULL,
  `email` varchar(254) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `campaña_id` (`campaña_id`),
  CONSTRAINT `enviados_ibfk_1` FOREIGN KEY (`campaña_id`) REFERENCES `campañas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: media_contacts
CREATE TABLE `media_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `secondary_language` varchar(30) DEFAULT NULL COMMENT 'Idioma alternativo o segundo idioma',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
