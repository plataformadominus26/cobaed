-- Esquema base de la plataforma COBAED (tablas vacías + catálogos compartidos).
-- Generado desde cobaedvillas el 2026-09-25. Lo usa: php deploy/migrar.php --instalar
SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `academias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academias` (
  `academia_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `book_id` int NOT NULL,
  PRIMARY KEY (`academia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `alumno_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `paterno` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `materno` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nacimiento` date NOT NULL,
  `pwd` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` int NOT NULL,
  `rems` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `semestre` int NOT NULL,
  `grupo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `book_id` int NOT NULL,
  `sexo` int NOT NULL,
  `direccion` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `imei` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `matricula` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `foto_thumb` int NOT NULL,
  `plantel_id` int NOT NULL,
  `domicilio` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `turno_id` int NOT NULL,
  `sangre_id` int NOT NULL,
  `seguro` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alergias` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `padecimientos` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `contacto1` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parentesco1` int NOT NULL,
  `tel1` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `contacto2` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parentesco2` int NOT NULL,
  `tel2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`alumno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `area_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `tipo_id` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `activa` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `areas_trn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas_trn` (
  `area_trn_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `tarea` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `foto` int DEFAULT NULL,
  `hora` time NOT NULL,
  `dia_1` int NOT NULL,
  `dia_2` int NOT NULL,
  `dia_3` int NOT NULL,
  `dia_4` int NOT NULL,
  `dia_5` int NOT NULL,
  `dia_6` int NOT NULL,
  `dia_7` int NOT NULL,
  `prioridad` int NOT NULL,
  PRIMARY KEY (`area_trn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cat_parentescos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cat_parentescos` (
  `parentesco_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(16) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`parentesco_id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `config_id` int NOT NULL AUTO_INCREMENT,
  `token` int NOT NULL,
  `book_id` int NOT NULL,
  `nombre` int NOT NULL,
  `valstr` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `valdte` date NOT NULL,
  `valint` int NOT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conocimiento_chunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conocimiento_chunks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `temario_id` int NOT NULL,
  `tipo` enum('definicion','ejemplo','analogia','formula','trampa_comun') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `contenido` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `origen` enum('ia_generado','maestro','pdf_dominio_publico') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'maestro',
  `validado_por` int DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `qdrant_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fuente_id` int DEFAULT NULL,
  `es_cita_textual` tinyint(1) DEFAULT '0',
  `creador_id` int NOT NULL,
  `estado` enum('borrador_privado','en_revision','aprobado_comunidad','rechazado') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'borrador_privado',
  `fecha_aprobacion` datetime DEFAULT NULL,
  `aprobado_por` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `temario_id` (`temario_id`),
  CONSTRAINT `conocimiento_chunks_ibfk_1` FOREIGN KEY (`temario_id`) REFERENCES `temas` (`tema_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fuentes_bibliograficas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuentes_bibliograficas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `materia_id` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `autor` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo_licencia` enum('dominio_publico','creative_commons','copyright_completo') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url_origen` varchar(512) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usar_memoria_ia` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `fuentes_bibliograficas_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `horario_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `dia` int NOT NULL,
  `hora` time NOT NULL,
  `hora1` time NOT NULL,
  `area_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  PRIMARY KEY (`horario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `materia_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `academia_id` int NOT NULL,
  `rama_id` int DEFAULT NULL COMMENT 'Enlace a ramas_ciencia para guiar el Prompt de la IA',
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `asignatura` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `semestre` int NOT NULL,
  `json_esquema` json DEFAULT NULL COMMENT 'Estructura que la IA debe respetar para esta rama',
  `meta` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `horas_clase` int NOT NULL,
  `horas_kobai` int NOT NULL,
  PRIMARY KEY (`materia_id`),
  KEY `academia_id` (`academia_id`),
  KEY `fk_materias_ramas` (`rama_id`),
  CONSTRAINT `fk_materias_ramas` FOREIGN KEY (`rama_id`) REFERENCES `ramas_ciencia` (`rama_id`),
  CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`academia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `periodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodos` (
  `periodo_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `book_id` int NOT NULL,
  `nombre` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `activo` int NOT NULL,
  PRIMARY KEY (`periodo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permisos_kb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos_kb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `academia_id` int DEFAULT NULL,
  `materia_id` int DEFAULT NULL,
  `nivel_acceso` enum('lector','editor','curador') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `planteles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planteles` (
  `plantel_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cct` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `municipio` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`plantel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posts_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts_blog` (
  `id_post` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `resumen` varchar(500) DEFAULT NULL,
  `estilo` enum('standard','alerta') DEFAULT 'standard',
  `categoria` varchar(50) DEFAULT 'general',
  `contenido_json` longtext NOT NULL,
  `autor_id` int DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estatus` tinyint(1) DEFAULT '1',
  `vistas` int DEFAULT '0',
  PRIMARY KEY (`id_post`),
  KEY `fecha_publicacion` (`fecha_publicacion`),
  KEY `estilo` (`estilo`),
  KEY `estatus` (`estatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `puestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `puestos` (
  `puesto_id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `token` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`puesto_id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qrcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qrcodes` (
  `qr_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `book_id` int NOT NULL,
  `tipo` int NOT NULL COMMENT '1=checkin, 2=biblioteca, 3=cafeteria',
  PRIMARY KEY (`qr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qrs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `qr` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `estacion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ramas_ciencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ramas_ciencia` (
  `rama_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ambito` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Agrupación mayor (Formales, Naturales, etc)',
  `descripcion_ia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Contexto extra para el Prompt de Gemini',
  PRIMARY KEY (`rama_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `book_id` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `inicio` datetime DEFAULT NULL,
  PRIMARY KEY (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles_trn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_trn` (
  `roltrn_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `menu_id` int DEFAULT NULL,
  PRIMARY KEY (`roltrn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tareas` (
  `tarea_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `clave` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `area_trn_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `tarea` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `prioridad` int DEFAULT NULL,
  `rems` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dia` int NOT NULL,
  PRIMARY KEY (`tarea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `temas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temas` (
  `tema_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `materia_id` int NOT NULL,
  `titulo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `unidad` int NOT NULL,
  `orden` int DEFAULT '0',
  PRIMARY KEY (`tema_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `temas_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipos_sangre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_sangre` (
  `sangre_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(5) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `orden` int DEFAULT NULL,
  PRIMARY KEY (`sangre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turnos` (
  `turno_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`turno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `book_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `nombre` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `celular` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `calle` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `colonia_id` int NOT NULL,
  `vigencia` date DEFAULT NULL,
  `puesto_id` int DEFAULT NULL,
  `usuario` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pwd` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `rems` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `inicio_id` int NOT NULL,
  `nivel` int NOT NULL,
  `cp` int NOT NULL,
  `llave` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `activo` int NOT NULL,
  `dpto_id` int NOT NULL,
  `horario_id` int NOT NULL,
  `ingreso` date NOT NULL,
  `imei` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`usuario_id`),
  KEY `token` (`token`),
  KEY `usuario` (`usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `puestos` WRITE;
/*!40000 ALTER TABLE `puestos` DISABLE KEYS */;
INSERT INTO `puestos` VALUES (0,1,'No especificado','bc7c17205fa19bb');
INSERT INTO `puestos` VALUES (1,1,'Maestro','3a2fa7a29881455');
INSERT INTO `puestos` VALUES (2,1,'Prefecto','d0e05b42d363d5a');
INSERT INTO `puestos` VALUES (3,1,'Intendencia','ba4ec029a38cbfc');
INSERT INTO `puestos` VALUES (4,1,'Vigilancia','ba0648482ac07c3');
INSERT INTO `puestos` VALUES (5,1,'Administrativo','315c76fff8bee84');
INSERT INTO `puestos` VALUES (6,1,'Directivo','acb1f46780f9ebd');
/*!40000 ALTER TABLE `puestos` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `turnos` WRITE;
/*!40000 ALTER TABLE `turnos` DISABLE KEYS */;
INSERT INTO `turnos` VALUES (1,'3f98690e50cb3753','Matutino');
INSERT INTO `turnos` VALUES (2,'4cb1455f8568bb53','Vespertino');
INSERT INTO `turnos` VALUES (3,'fa7e28dd8d6b3a1e','Nocturno');
INSERT INTO `turnos` VALUES (4,'2e2c7f41d6ffea87','Fin de Semana');
/*!40000 ALTER TABLE `turnos` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `tipos_sangre` WRITE;
/*!40000 ALTER TABLE `tipos_sangre` DISABLE KEYS */;
INSERT INTO `tipos_sangre` VALUES (1,'O+','Positivo',1);
INSERT INTO `tipos_sangre` VALUES (2,'A+','Positivo',2);
INSERT INTO `tipos_sangre` VALUES (3,'B+','Positivo',3);
INSERT INTO `tipos_sangre` VALUES (4,'O-','Negativo (Donante Universal)',4);
INSERT INTO `tipos_sangre` VALUES (5,'A-','Negativo',5);
INSERT INTO `tipos_sangre` VALUES (6,'AB+','Positivo (Receptor Universal)',6);
INSERT INTO `tipos_sangre` VALUES (7,'B-','Negativo',7);
INSERT INTO `tipos_sangre` VALUES (8,'AB-','Negativo',8);
/*!40000 ALTER TABLE `tipos_sangre` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `cat_parentescos` WRITE;
/*!40000 ALTER TABLE `cat_parentescos` DISABLE KEYS */;
INSERT INTO `cat_parentescos` VALUES (1,'7f3e1a2b','Abuela');
INSERT INTO `cat_parentescos` VALUES (2,'2d5c8b9a','Abuelo');
INSERT INTO `cat_parentescos` VALUES (3,'9e4f1c3d','Hermana');
INSERT INTO `cat_parentescos` VALUES (4,'5a6b7c8d','Hermano');
INSERT INTO `cat_parentescos` VALUES (5,'1a2b3c4d','Madre');
INSERT INTO `cat_parentescos` VALUES (6,'8d7c6b5a','Padre');
INSERT INTO `cat_parentescos` VALUES (7,'3f4e5d6c','Tía');
INSERT INTO `cat_parentescos` VALUES (8,'c7d8e9f0','Tío');
INSERT INTO `cat_parentescos` VALUES (9,'b1a2c3d4','Tutor / Representante Legal');
/*!40000 ALTER TABLE `cat_parentescos` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `academias` WRITE;
/*!40000 ALTER TABLE `academias` DISABLE KEYS */;
INSERT INTO `academias` VALUES (1,'8DD75BCAB3E939C','Pensamiento Matemático','Academia de Ciencias Exactas, Lógica y Razonamiento Cuantitativo',1);
INSERT INTO `academias` VALUES (2,'912DF66A9D1BAEC','Cultura Digital','Tecnologías de la Información, Programación y Ciudadanía Digital',1);
INSERT INTO `academias` VALUES (3,'A33F5BCAB3E888D','Lengua y Comunicación','Taller de Lectura, Redacción, Inglés y Literatura',1);
INSERT INTO `academias` VALUES (4,'B44E6BCAB3E777E','Humanidades y Cs. Sociales','Historia, Ética, Filosofía y Estructuras Socioeconómicas',1);
/*!40000 ALTER TABLE `academias` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `ramas_ciencia` WRITE;
/*!40000 ALTER TABLE `ramas_ciencia` DISABLE KEYS */;
INSERT INTO `ramas_ciencia` VALUES (1,'FORM_001','Matemáticas','Ciencias Formales','Enfócate en cantidades, estructuras, demostraciones lógicas y axiomas. Usa LaTeX.');
INSERT INTO `ramas_ciencia` VALUES (2,'FORM_002','Lógica','Ciencias Formales','Enfócate en la inferencia válida, premisas, conclusiones y falacias.');
INSERT INTO `ramas_ciencia` VALUES (3,'FORM_003','Informática','Ciencias Formales','Enfócate en algoritmos, estructuras de datos, computación y código.');
INSERT INTO `ramas_ciencia` VALUES (4,'NAT_001','Física','Ciencias Naturales','Enfócate en materia, energía, leyes del movimiento y termodinámica. Usa ejemplos experimentales.');
INSERT INTO `ramas_ciencia` VALUES (5,'NAT_002','Química','Ciencias Naturales','Enfócate en composición molecular, reacciones y propiedades de la materia.');
INSERT INTO `ramas_ciencia` VALUES (6,'NAT_003','Biología','Ciencias Naturales','Enfócate en seres vivos, células, genética y ecosistemas.');
INSERT INTO `ramas_ciencia` VALUES (7,'NAT_004','Astronomía','Ciencias Naturales','Enfócate en cuerpos celestes, cosmología y fenómenos del universo.');
INSERT INTO `ramas_ciencia` VALUES (8,'NAT_005','Geología','Ciencias Naturales','Enfócate en la estructura terrestre, placas tectónicas y procesos geológicos.');
INSERT INTO `ramas_ciencia` VALUES (9,'SOC_001','Historia','Ciencias Sociales','Enfócate en cronologías, causas y consecuencias de eventos pasados y contexto cultural.');
INSERT INTO `ramas_ciencia` VALUES (10,'SOC_002','Sociología','Ciencias Sociales','Enfócate en estructuras sociales, relaciones humanas y fenómenos colectivos.');
INSERT INTO `ramas_ciencia` VALUES (11,'SOC_003','Psicología','Ciencias Sociales','Enfócate en procesos mentales, comportamiento humano y teorías cognitivas.');
INSERT INTO `ramas_ciencia` VALUES (12,'SOC_004','Economía','Ciencias Sociales','Enfócate en producción, distribución, mercados y modelos micro/macroeconómicos.');
INSERT INTO `ramas_ciencia` VALUES (13,'SOC_005','Lingüística','Ciencias Sociales','Enfócate en la estructura del lenguaje, semántica, sintaxis y fonética.');
INSERT INTO `ramas_ciencia` VALUES (14,'HUM_001','Filosofía','Humanidades','Enfócate en el pensamiento crítico, ética, existencia y argumentos racionales.');
INSERT INTO `ramas_ciencia` VALUES (15,'HUM_002','Literatura','Humanidades','Enfócate en análisis de textos, figuras retóricas, narrativa y estética.');
INSERT INTO `ramas_ciencia` VALUES (16,'APP_001','Medicina','Ciencias Aplicadas','Enfócate en salud humana, anatomía, patologías y tratamientos clínicos.');
INSERT INTO `ramas_ciencia` VALUES (17,'APP_002','Ingeniería','Ciencias Aplicadas','Enfócate en diseño, construcción, sistemas y aplicación práctica de principios científicos.');
INSERT INTO `ramas_ciencia` VALUES (18,'APP_003','Administración','Ciencias Aplicadas','Enfócate en gestión de recursos, organizaciones, planeación y estrategia.');
/*!40000 ALTER TABLE `ramas_ciencia` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'21E9F3C783741E4',1,1,'algebra','Pensamiento Matemático I',1,NULL,'Comprender el lenguaje algebraico y la estadística básica para la toma de decisiones.',4,0);
INSERT INTO `materias` VALUES (2,'7D494FF79CE94A5',1,1,'trigonometria','Pensamiento Matemático II',2,NULL,'Aplicar la geometría y trigonometría en la resolución de problemas espaciales.',4,0);
INSERT INTO `materias` VALUES (3,'57AD26637B663C3',1,1,'geometria analitica','Pensamiento Matemático III',3,NULL,'Analizar lugares geométricos y geometría analítica.',4,0);
INSERT INTO `materias` VALUES (4,'88BB26637B663D4',1,1,'Cálculo Diferencial','Pensamiento Matemático IV',4,NULL,'Estudio del cambio y la variación mediante derivadas.',5,0);
INSERT INTO `materias` VALUES (5,'99CC26637B663E5',2,3,'herramientas digitales','Cultura Digital I',1,NULL,'Identidad digital, seguridad y herramientas de colaboración.',3,0);
INSERT INTO `materias` VALUES (6,'00DD26637B663F6',2,3,'bases digitales','Cultura Digital II',2,NULL,'Pensamiento algorítmico y bases de programación.',3,0);
INSERT INTO `materias` VALUES (7,'AAEE26637B663G7',3,13,'escritura','Lengua y Comunicación I',1,NULL,'Procesos de lectura y escritura de textos académicos.',4,0);
INSERT INTO `materias` VALUES (8,'BBFF26637B663H8',3,15,'literatura','Literatura I',3,NULL,'La literatura como arte y expresión estética.',3,0);
INSERT INTO `materias` VALUES (9,'CC0026637B663I9',4,9,'historia','La Conciencia Histórica I',3,NULL,'Análisis de los procesos históricos de la nación.',3,0);
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS=1;
