<?php
/*
 * ============================================================================
 *  AulaFix · includes/db.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final (en el Hito 1 los datos van en un array de PHP)
 *  RA:     0613-RA6 (conexión en una única función, PDO, sentencias
 *          preparadas, utf8mb4, transacción para estado + historial)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Todo el acceso a la base de datos: la función que crea la conexión PDO
 *  y las funciones (o la clase) que hacen las consultas de la aplicación.
 *  Las páginas de public/ NUNCA escriben SQL directamente: llaman a lo que
 *  hay aquí.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] La conexión se crea en UNA ÚNICA función (por ejemplo bd(): PDO).
 *  [ ] Los datos de conexión salen de config.php (nada de contraseñas aquí).
 *  [ ] DSN de MySQL con charset=utf8mb4.
 *  [ ] Siempre prepare() + execute(). NUNCA concatenar datos del usuario en
 *      el SQL (eso es inyección SQL y suspende el proyecto).
 *  [ ] Cada función con su PHPDoc (@param, @return) y cada consulta comentada.
 *
 *  Consultas que va a necesitar la aplicación (os proponemos nombres):
 *  [ ] buscarUsuarioPorEmail(string $email): ?array       → login / registro
 *  [ ] crearUsuario(string $nombre, string $email, string $hash): int   → registro
 *  [ ] listarEquipos(): array                              → desplegable de nueva.php
 *  [ ] listarIncidencias(): array                          → incidencias.php (tabla + JSON)
 *  [ ] buscarIncidencia(int $id): ?array                   → incidencia.php
 *  [ ] historialDe(int $incidenciaId): array               → incidencia.php
 *  [ ] crearIncidencia(...): int                           → nueva.php
 *  [ ] cambiarEstado(int $id, string $nuevo, string $comentario, int $usuarioId): void
 *        → UPDATE de la incidencia + INSERT en historial EN UNA TRANSACCIÓN
 *  [ ] borrarIncidencia(int $id): void                      → incidencia.php
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Mirad el ejemplo de bd() de los apuntes (Unidad5PHP/6_ArquitecturaEnCapas.php):
 *    usa una variable "static" para crear la conexión UNA sola vez por petición.
 *    ¿Qué ventaja tiene frente a hacer new PDO() en cada página?
 *  - Opciones de PDO que deberíais investigar y comentar:
 *      ATTR_ERRMODE → ERRMODE_EXCEPTION   (los errores lanzan excepciones)
 *      ATTR_DEFAULT_FETCH_MODE → FETCH_ASSOC (filas como arrays asociativos)
 *      ATTR_EMULATE_PREPARES → false       (preparadas reales en MySQL)
 *  - listarIncidencias() tiene que devolver lo que pide el «contrato» JSON
 *    (apartado 3.4): el CÓDIGO del equipo y el NOMBRE del autor, no sus ids.
 *    → Necesitáis un JOIN con equipos y usuarios.
 *  - El contrato pide creada_en en formato ISO 8601 ("2026-11-02T18:30:00+01:00").
 *    MySQL os da "2026-11-02 18:30:00". Investigad DateTimeImmutable y el
 *    formato DATE_ATOM (o 'c') para convertirlo.
 *  - Transacción: beginTransaction() → consultas → commit(). Si algo lanza
 *    una excepción → rollBack() en el catch y volved a lanzarla.
 *    PREGUNTA DE DEFENSA: ¿qué pasaría sin la transacción si el UPDATE sale
 *    bien pero el INSERT del historial falla?
 *  - Para cambiarEstado necesitáis el estado ANTERIOR: ¿lo leéis antes del
 *    UPDATE dentro de la misma transacción?
 *  - Alternativa más "POO": una clase final RepositorioIncidencias que recibe
 *    el PDO en el constructor (como en los apuntes). Cualquiera de las dos
 *    opciones es válida si está comentada.
 *  - fetch() devuelve false si no hay fila. ¿Cómo convertís eso en null para
 *    que el tipo de retorno ?array sea correcto?
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - PDO: https://www.php.net/manual/es/book.pdo.php
 *  - DSN de MySQL: https://www.php.net/manual/es/ref.pdo-mysql.connection.php
 *  - Sentencias preparadas: https://www.php.net/manual/es/pdo.prepared-statements.php
 *  - Transacciones: https://www.php.net/manual/es/pdo.transactions.php
 *  - Variables estáticas en funciones: https://www.php.net/manual/es/language.variables.scope.php
 *  - SQL: INNER JOIN, ORDER BY, LIMIT
 *  - Inyección SQL (OWASP): https://owasp.org/www-community/attacks/SQL_Injection
 */

