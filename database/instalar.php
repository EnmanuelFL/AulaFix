<?php
/*
 * ============================================================================
 *  AulaFix · database/instalar.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final
 *  RA:     0613-RA6 (Sobresaliente: script de instalación con datos de ejemplo)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Un script de CONSOLA (no es una página web) que crea la base de datos a
 *  partir de esquema.sql y la llena con datos de ejemplo. Se ejecuta así,
 *  desde la raíz del proyecto:
 *
 *      php database/instalar.php
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Usar la conexión de includes/db.php (y la configuración de config.php).
 *  [ ] Leer y ejecutar database/esquema.sql.
 *  [ ] Insertar datos de ejemplo:
 *        · usuarios: al menos 1 profesor y 2 alumnos (con su contraseña
 *          guardada con password_hash + Argon2id, NUNCA en claro).
 *        · equipos: varios (PC-01, PC-02, PROY-01, IMP-01...).
 *        · incidencias: variadas en estado, prioridad, categoría, autor y FECHA
 *          (así podréis probar el resumen, las pestañas, los urgentes y el
 *          «hace X días» del panel).
 *        · historial: alguna línea para las que ya no están "abiertas".
 *  [ ] Siempre con sentencias preparadas (prepare + execute).
 *  [ ] Los usuarios de prueba (correo + contraseña) tienen que aparecer en el
 *      README.md.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Proteged el script para que solo funcione desde la consola. Investigad
 *    la constante PHP_SAPI o la función php_sapi_name(). ¿Qué valor tiene
 *    cuando se ejecuta con "php fichero.php"?
 *  - Leer el SQL: file_get_contents() + __DIR__ para construir la ruta, así
 *    funciona da igual desde qué carpeta lo lancéis.
 *  - PDO::exec() ejecuta SQL sin parámetros. Si os da problemas con varias
 *    sentencias seguidas, podéis separar el fichero por ";" y ejecutar una a una.
 *  - Si la base de datos todavía no existe, la conexión fallará porque el DSN
 *    lleva dbname=... Opciones: crearla a mano/phpMyAdmin, que la cree Docker
 *    (MYSQL_DATABASE) o conectar sin dbname y lanzar CREATE DATABASE IF NOT EXISTS.
 *  - Meter todos los INSERT dentro de una TRANSACCIÓN: si algo falla a mitad,
 *    no se queda la base de datos a medias.
 *  - Para insertar varias filas parecidas: un solo prepare() y varios
 *    execute() dentro de un foreach sobre un array de datos.
 *  - lastInsertId() os da el id que acaba de generar MySQL (útil para crear
 *    después las incidencias de ese usuario/equipo).
 *  - Fechas variadas: podéis calcularlas con DateTimeImmutable y modify('-3 days').
 *  - Al terminar, mostrad por consola un resumen (cuántos usuarios,
 *    equipos e incidencias se han creado).
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - PHP en línea de comandos (CLI): https://www.php.net/manual/es/features.commandline.php
 *  - PDO::exec / prepare / execute: https://www.php.net/manual/es/book.pdo.php
 *  - Transacciones (beginTransaction, commit, rollBack)
 *  - PDO::lastInsertId: https://www.php.net/manual/es/pdo.lastinsertid.php
 *  - password_hash con PASSWORD_ARGON2ID: https://www.php.net/manual/es/function.password-hash.php
 *  - DateTimeImmutable::modify: https://www.php.net/manual/es/datetimeimmutable.modify.php
 */

