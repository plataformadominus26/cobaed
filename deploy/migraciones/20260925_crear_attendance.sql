-- Asistencia: presencia docente en aula (movil/) y reloj checador (checador/).
-- Solo se crea donde no existe (Villas la perdió el 16-sep-2026; Lomas conserva la suya).
-- checkin/checkout son TIME: movil guarda la hora de la clase y checador usa CURTIME().
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` INT NOT NULL AUTO_INCREMENT,
  `token`         VARCHAR(32) NULL,
  `fecha`         DATETIME NULL,
  `usuario_id`    INT NULL COMMENT 'quien registra (prefecto) o empleado en checador',
  `checkin`       TIME NULL,
  `checkout`      TIME NULL,
  `estado`        TINYINT NULL COMMENT '1=falta 2=retraso 3=asiste',
  `area_id`       INT NULL,
  `rems`          VARCHAR(255) NULL,
  `maestro_id`    INT NULL,
  `dia`           DATE GENERATED ALWAYS AS (DATE(`fecha`)) STORED,
  `creado`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uq_clase` (`maestro_id`, `area_id`, `dia`, `checkin`),
  KEY `idx_usuario_fecha` (`usuario_id`, `fecha`),
  KEY `idx_maestro_fecha` (`maestro_id`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
