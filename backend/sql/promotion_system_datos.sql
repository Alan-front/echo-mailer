
--
-- Base de datos: `promotion_system`
--

--
-- Volcado de datos para la tabla `media_contacts`
--

INSERT INTO `media_contacts` (`id`, `email`, `name`, `media_type`, `music_genre`, `country`, `language`, `website`, `phone`, `notes`, `active`, `created_at`, `test_email`, `secondary_language`) VALUES
(1, 'test@testmail.com', 'Radio De la Vie', 'Radio', 'Jazz', 'France', 'French', 'wwww.test.com', '', 'radio jazz de madrugada', 1, '2025-06-09 05:31:14', 'test@testemail.com', 'French'),
(2, 'test@testmail.com', 'Jazz Radio UK', 'Radio', 'Jazz', 'United Kingdom', 'English', 'http://jazzuk.com', '', 'Radio de jazz nocturna.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'English'),
(3, 'test@testmail.com', 'Seoul Jazz FM', 'Radio', 'Jazz', 'South Korea', 'Korean', 'http://seouljazz.kr', '', 'Estación de jazz contemporáneo.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'Korean'),
(4, 'test@testmail.com', 'Tokyo Jazz Channel', 'Radio', 'Jazz', 'Japan', 'Japanese', 'http://tokyojazz.jp', '', 'Jazz clásico y moderno.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'Japanese'),
(5, 'test@testmail.com', 'Kyiv Jazz Station', 'Radio', 'Jazz', 'Ukraine', 'Ukrainian', 'http://kyivjazz.ua', '', 'Jazz local y europeo.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'Ukrainian'),
(6, 'test@testmail.com', 'Radio Ruso Jazz', 'Radio', 'Jazz', 'Russia', 'Russian', 'http://russianjazz.ru', '', 'Jazz ruso y mundial.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'Russian'),
(7, 'test@testmail.com', 'New York Jazz Live', 'Radio', 'Jazz', 'USA', 'English', 'http://nyjazzlive.com', '', 'Jazz en vivo desde NYC.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'English'),
(8, 'test@testmail.com', 'Radio Jazz España', 'Radio', 'Jazz', 'Spain', 'Spanish', 'http://radiojazz.es', '', 'Jazz para todos.', 1, '2025-06-09 05:36:41', 'test@testemail.com', 'Spanish'),
(9, 'test@testmail.com', 'Radio Francaise', 'Radio', 'World Music', 'France', 'French', 'http://radiofrtest.com', '123456789', 'Estación dedicada a música del mundo.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'French'),
(10, 'test@testmail.com', 'Global Beat TV', 'TV', 'World Music', 'USA', 'English', 'http://globalbeattv.com', '987654321', 'Programa televisivo sobre música global.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'English'),
(11, 'test@testmail.com', 'Tokyo World Magazine', 'Magazine', 'World Music', 'Japan', 'Japanese', 'http://tokyoworldmag.jp', '555555555', 'Revista sobre música mundial.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'Japanese'),
(12, 'test@testmail.com', 'Kyiv World Radio', 'Radio', 'World Music', 'Ukraine', 'Ukrainian', 'http://kyivworldradio.ua', '444444444', 'Radio con enfoque en música del mundo.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'Ukrainian'),
(13, 'test@testmail.com', 'Radio Russkaya Planeta', 'Radio', 'World Music', 'Russia', 'Russian', 'http://russianplaneta.ru', '333333333', 'Estación de radio que cubre música del mundo.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'Russian'),
(14, 'test@testmail.com', 'Sonidos Latinos Magazine', 'Magazine', 'World Music', 'Spain', 'Spanish', 'http://sonidoslatinos.es', '222222222', 'Revista dedicada a música del mundo.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'Spanish'),
(15, 'test@testmail.com', 'Seoul World TV', 'TV', 'World Music', 'South Korea', 'Korean', 'http://seoulworldtv.kr', '111111111', 'Programa televisivo de música mundial.', 1, '2025-06-09 05:40:25', 'test@testemail.com', 'Korean'),
(16, 'test@testmail.com', 'Radio Classique France', 'Radio', 'Classical', 'France', 'French', 'http://radioclassiquefr.fr', '123450000', 'Emisora dedicada a música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'French'),
(17, 'test@testmail.com', 'Classic Sounds USA', 'TV', 'Classical', 'USA', 'English', 'http://classicsoundsusa.com', '987650000', 'Programa de televisión sobre música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'English'),
(18, 'test@testmail.com', 'Tokyo Classical Magazine', 'Magazine', 'Classical', 'Japan', 'Japanese', 'http://tokyoclassical.jp', '555550000', 'Revista especializada en música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'Japanese'),
(19, 'test@testmail.com', 'Kyiv Classical Radio', 'Radio', 'Classical', 'Ukraine', 'Ukrainian', 'http://kyivclassical.ua', '444440000', 'Estación de radio para música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'Ukrainian'),
(20, 'test@testmail.com', 'Russian Classical Radio', 'Radio', 'Classical', 'Russia', 'Russian', 'http://russianclassical.ru', '333330000', 'Emisora clásica rusa.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'Russian'),
(21, 'test@testmail.com', 'Clásica España Magazine', 'Magazine', 'Classical', 'Spain', 'Spanish', 'http://clasicaespana.es', '222220000', 'Revista española de música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'Spanish'),
(22, 'test@testmail.com', 'Seoul Classical TV', 'TV', 'Classical', 'South Korea', 'Korean', 'http://seoulclassical.kr', '111110000', 'Programa televisivo de música clásica.', 1, '2025-06-09 05:41:55', 'test@testemail.com', 'Korean'),
(23, 'test@testmail.com', 'Radio Rock France', 'Radio', 'Rock', 'France', 'French', 'http://radiorockfrance.fr', '123450001', 'Emisora dedicada a rock francés.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'French'),
(24, 'test@testmail.com', 'Rock USA TV', 'TV', 'Rock', 'USA', 'English', 'http://rockusatelevision.com', '987650001', 'Programa de televisión sobre rock americano.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'English'),
(25, 'test@testmail.com', 'Tokyo Rock Magazine', 'Magazine', 'Rock', 'Japan', 'Japanese', 'http://tokyorockmag.jp', '555550001', 'Revista especializada en rock japonés.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'Japanese'),
(26, 'test@testmail.com', 'Kyiv Rock Radio', 'Radio', 'Rock', 'Ukraine', 'Ukrainian', 'http://kyivrock.ua', '444440001', 'Emisora de rock en Ucrania.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'Ukrainian'),
(27, 'test@testmail.com', 'Russian Rock Radio', 'Radio', 'Rock', 'Russia', 'Russian', 'http://russianrock.ru', '333330001', 'Estación rusa dedicada al rock.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'Russian'),
(28, 'test@testmail.com', 'Rock España Magazine', 'Magazine', 'Rock', 'Spain', 'Spanish', 'http://rockespana.es', '222220001', 'Revista española sobre rock.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'Spanish'),
(29, 'test@testmail.com', 'Seoul Rock TV', 'TV', 'Rock', 'South Korea', 'Korean', 'http://seoulrock.kr', '111110001', 'Programa televisivo sobre rock coreano.', 1, '2025-06-09 05:42:21', 'test@testemail.com', 'Korean'),
(30, 'test@testmail.com', 'Radio Pop France', 'Radio', 'Pop', 'France', 'French', 'http://radiopopfrance.fr', '123450101', 'Emisora dedicada a pop francés.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'French'),
(31, 'test@testmail.com', 'Pop USA TV', 'TV', 'Pop', 'USA', 'English', 'http://popusatelevision.com', '987650102', 'Programa de televisión sobre pop americano.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'English'),
(32, 'test@testmail.com', 'Tokyo Pop Magazine', 'Magazine', 'Pop', 'Japan', 'Japanese', 'http://tokyopopmag.jp', '555550103', 'Revista especializada en pop japonés.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'Japanese'),
(33, 'test@testmail.com', 'Kyiv Pop Radio', 'Radio', 'Pop', 'Ukraine', 'Ukrainian', 'http://kyivpop.ua', '444440104', 'Emisora de pop en Ucrania.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'Ukrainian'),
(34, 'test@testmail.com', 'Russian Pop Radio', 'Radio', 'Pop', 'Russia', 'Russian', 'http://russianpop.ru', '333330105', 'Estación rusa dedicada al pop.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'Russian'),
(35, 'test@testmail.com', 'Pop España Magazine', 'Magazine', 'Pop', 'Spain', 'Spanish', 'http://popespana.es', '222220106', 'Revista española sobre pop.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'Spanish'),
(36, 'test@testmail.com', 'Seoul Pop TV', 'TV', 'Pop', 'South Korea', 'Korean', 'http://seoulpop.kr', '111110107', 'Programa televisivo sobre pop coreano.', 1, '2025-06-09 05:43:05', 'test@testemail.com', 'Korean');
COMMIT;
