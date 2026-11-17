-- ============================================================================
--  AulaFix · database/esquema.sql
-- ============================================================================
--  Crea las 4 tablas de la aplicación en MySQL 8 (motor InnoDB, utf8mb4).
--  No lleva datos: los carga database/instalar.php después de ejecutar este script.
--  Los valores de los ENUM coinciden con las listas blancas de config.php.
--  Se borran primero las tablas (en orden inverso a sus dependencias) para poder
--  reinstalar. OJO: esto elimina los datos, solo se usa en desarrollo.
-- ============================================================================

DROP TABLE IF EXISTS historial;
DROP TABLE IF EXISTS incidencias;
DROP TABLE IF EXISTS equipos;
DROP TABLE IF EXISTS usuarios;

-- Usuarios: el email es único (no puede haber dos cuentas con el mismo correo)
-- clave_hash guarda el hash Argon2id, nunca la contraseña (255 es el tamaño recomendado)
CREATE TABLE usuarios (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre     VARCHAR(80)  NOT NULL,
    email      VARCHAR(190) NOT NULL,
    clave_hash VARCHAR(255) NOT NULL,
    rol        ENUM('alumno', 'profesor') NOT NULL DEFAULT 'alumno',
    creado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Equipos del aula: el código es único (PC-01, PROY-01...)
CREATE TABLE equipos (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo      VARCHAR(20)  NOT NULL,
    descripcion VARCHAR(150) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_equipos_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Incidencias: claves foráneas a equipos y usuarios
-- ON DELETE RESTRICT impide borrar un equipo o un usuario que tenga incidencias (así no se pierde información)
-- categoria, prioridad y estado solo admiten los valores permitidos (ENUM)
CREATE TABLE incidencias (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    equipo_id      INT UNSIGNED NOT NULL,
    usuario_id     INT UNSIGNED NOT NULL,
    categoria      ENUM('hardware', 'software', 'red', 'otro') NOT NULL,
    prioridad      ENUM('baja', 'media', 'alta') NOT NULL,
    descripcion    VARCHAR(500) NOT NULL,
    estado         ENUM('abierta', 'en_curso', 'resuelta') NOT NULL DEFAULT 'abierta',
    creada_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_incidencias_equipo  FOREIGN KEY (equipo_id)  REFERENCES equipos (id)  ON DELETE RESTRICT,
    CONSTRAINT fk_incidencias_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historial de cambios de estado de cada incidencia
-- ON DELETE CASCADE: al borrar una incidencia se borra automáticamente su historial
CREATE TABLE historial (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    incidencia_id   INT UNSIGNED NOT NULL,
    usuario_id      INT UNSIGNED NOT NULL,
    estado_anterior ENUM('abierta', 'en_curso', 'resuelta') NOT NULL,
    estado_nuevo    ENUM('abierta', 'en_curso', 'resuelta') NOT NULL,
    comentario      VARCHAR(500) NOT NULL,
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_historial_incidencia FOREIGN KEY (incidencia_id) REFERENCES incidencias (id) ON DELETE CASCADE,
    CONSTRAINT fk_historial_usuario    FOREIGN KEY (usuario_id)    REFERENCES usuarios (id)    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
