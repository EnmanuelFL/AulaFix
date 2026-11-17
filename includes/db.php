<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · includes/db.php
 * ============================================================================
 *  Todo el acceso a MySQL: la conexión PDO (en una única función) y las consultas
 *  de la aplicación. Las páginas de public/ nunca escriben SQL: llaman a estas funciones.
 *  Regla de oro: SIEMPRE prepare() + execute(), nunca concatenar datos del usuario en el SQL
 *  (si no, habría inyección SQL).
 */

require_once __DIR__ . '/../config.php';

/**
 * Devuelve la conexión PDO a MySQL. Se crea una sola vez por petición (variable static).
 *
 * @return PDO Conexión lista para usar.
 */
function bd(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // los errores lanzan excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // filas como arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                   // sentencias preparadas reales de MySQL
            ]);
            // La sesión de MySQL usa la misma hora que PHP, así CURRENT_TIMESTAMP y las fechas de PHP coinciden
            $pdo->exec("SET time_zone = '" . (new DateTimeImmutable())->format('P') . "'");
        } catch (PDOException $e) {
            // El detalle técnico va al log: nunca se enseña al usuario (podría revelar datos de la conexión)
            error_log('AulaFix: error de conexión a la base de datos: ' . $e->getMessage());
            http_response_code(500);
            exit('No se puede conectar con la base de datos.');
        }
    }

    return $pdo;
}

/**
 * Convierte una fecha de MySQL (2026-11-02 18:30:00) al formato ISO 8601 del contrato JSON.
 *
 * @param string $fechaMysql Fecha tal y como la devuelve MySQL.
 * @return string Fecha ISO 8601 con zona horaria (2026-11-02T18:30:00+01:00).
 */
function fechaIso(string $fechaMysql): string
{
    return (new DateTimeImmutable($fechaMysql))->format(DATE_ATOM);
}

/**
 * Busca un usuario por su correo (para el login y para comprobar que no esté repetido en el registro).
 *
 * @param string $email Correo a buscar.
 * @return array|null Fila del usuario o null si no existe.
 */
function buscarUsuarioPorEmail(string $email): ?array
{
    $st = bd()->prepare('SELECT id, nombre, email, clave_hash, rol FROM usuarios WHERE email = ?');
    $st->execute([$email]);
    return $st->fetch() ?: null;
}

/**
 * Crea un usuario nuevo con rol alumno. Los profesores solo se crean desde instalar.php,
 * porque si el rol viniera del formulario cualquiera podría registrarse como profesor.
 *
 * @param string $nombre Nombre completo.
 * @param string $email  Correo (único).
 * @param string $hash   Hash Argon2id de la contraseña.
 * @return int Id del usuario creado.
 */
function crearUsuario(string $nombre, string $email, string $hash): int
{
    $st = bd()->prepare("INSERT INTO usuarios (nombre, email, clave_hash, rol) VALUES (?, ?, ?, 'alumno')");
    $st->execute([$nombre, $email, $hash]);
    return (int) bd()->lastInsertId();
}

/**
 * Lista los equipos del aula (para el desplegable de nueva.php).
 *
 * @return array Lista de equipos [id, codigo, descripcion].
 */
function listarEquipos(): array
{
    // Se fuerza que 'Otros..' aparezca siempre al final del desplegable
    $filas = bd()->query("SELECT id, codigo, descripcion FROM equipos ORDER BY (codigo = 'Otros..') ASC, codigo ASC")->fetchAll();
    return array_map(static fn(array $f): array => [
        'id'          => (int) $f['id'],
        'codigo'      => $f['codigo'],
        'descripcion' => $f['descripcion'],
    ], $filas);
}

/**
 * Lista todas las incidencias con el formato exacto del «contrato» JSON que usa el cliente.
 * Se hace un JOIN para devolver el CÓDIGO del equipo y el NOMBRE del autor, no sus ids.
 *
 * @return array Lista de incidencias (id, equipo, categoria, prioridad, descripcion, estado, autor, creada_en).
 */
function listarIncidencias(): array
{
    $sql = 'SELECT i.id, e.codigo AS equipo, i.categoria, i.prioridad, i.descripcion,
                   i.estado, u.nombre AS autor, i.creada_en
            FROM incidencias i
            INNER JOIN equipos  e ON e.id = i.equipo_id
            INNER JOIN usuarios u ON u.id = i.usuario_id
            ORDER BY i.creada_en DESC, i.id DESC';

    return array_map(static fn(array $f): array => [
        'id'          => (int) $f['id'],
        'equipo'      => $f['equipo'],
        'categoria'   => $f['categoria'],
        'prioridad'   => $f['prioridad'],
        'descripcion' => $f['descripcion'],
        'estado'      => $f['estado'],
        'autor'       => $f['autor'],
        'creada_en'   => fechaIso($f['creada_en']),
    ], bd()->query($sql)->fetchAll());
}

/**
 * Busca una incidencia por su id con los datos de su equipo y su autor.
 *
 * @param int $id Id de la incidencia.
 * @return array|null Datos de la incidencia o null si no existe.
 */
function buscarIncidencia(int $id): ?array
{
    $sql = 'SELECT i.id, i.usuario_id, e.codigo AS equipo, i.categoria, i.prioridad, i.descripcion,
                   i.estado, u.nombre AS autor, i.creada_en, i.actualizada_en
            FROM incidencias i
            INNER JOIN equipos  e ON e.id = i.equipo_id
            INNER JOIN usuarios u ON u.id = i.usuario_id
            WHERE i.id = ?';
    $st = bd()->prepare($sql);
    $st->execute([$id]);
    $f = $st->fetch();
    if ($f === false) {
        return null;
    }

    $f['id']             = (int) $f['id'];
    $f['usuario_id']     = (int) $f['usuario_id'];
    $f['creada_en']      = fechaIso($f['creada_en']);
    $f['actualizada_en'] = fechaIso($f['actualizada_en']);
    return $f;
}

/**
 * Devuelve el historial de cambios de estado de una incidencia, del más antiguo al más reciente.
 *
 * @param int $incidenciaId Id de la incidencia.
 * @return array Lista de cambios (estado_anterior, estado_nuevo, comentario, creado_en, autor).
 */
function historialDe(int $incidenciaId): array
{
    $sql = 'SELECT h.estado_anterior, h.estado_nuevo, h.comentario, h.creado_en, u.nombre AS autor
            FROM historial h
            INNER JOIN usuarios u ON u.id = h.usuario_id
            WHERE h.incidencia_id = ?
            ORDER BY h.creado_en ASC, h.id ASC';
    $st = bd()->prepare($sql);
    $st->execute([$incidenciaId]);

    return array_map(static function (array $f): array {
        $f['creado_en'] = fechaIso($f['creado_en']);
        return $f;
    }, $st->fetchAll());
}

/**
 * Crea una incidencia nueva. El estado inicial es siempre 'abierta' (lo pone la BD por defecto).
 *
 * @param int    $equipoId    Id del equipo afectado.
 * @param int    $usuarioId   Id del usuario que avisa (sale de la sesión, nunca del formulario).
 * @param string $categoria   Categoría (ya validada con la lista blanca).
 * @param string $prioridad   Prioridad (ya validada con la lista blanca).
 * @param string $descripcion Descripción del problema.
 * @return int Id de la incidencia creada.
 */
function crearIncidencia(int $equipoId, int $usuarioId, string $categoria, string $prioridad, string $descripcion): int
{
    $st = bd()->prepare(
        'INSERT INTO incidencias (equipo_id, usuario_id, categoria, prioridad, descripcion) VALUES (?, ?, ?, ?, ?)'
    );
    $st->execute([$equipoId, $usuarioId, $categoria, $prioridad, $descripcion]);
    return (int) bd()->lastInsertId();
}

/**
 * Cambia el estado de una incidencia y añade la línea al historial EN UNA TRANSACCIÓN.
 * Si fallara el INSERT del historial, el UPDATE se deshace (rollback) y no queda un
 * cambio de estado sin su registro.
 *
 * @param int    $id         Id de la incidencia.
 * @param string $nuevo      Nuevo estado (ya validado con la lista blanca).
 * @param string $comentario Motivo del cambio.
 * @param int    $usuarioId  Id del profesor que hace el cambio.
 * @return void
 * @throws Throwable Si algo falla (después de deshacer la transacción).
 */
function cambiarEstado(int $id, string $nuevo, string $comentario, int $usuarioId): void
{
    $pdo = bd();

    try {
        $pdo->beginTransaction();

        // FOR UPDATE bloquea la fila para que nadie la cambie a la vez mientras la leemos
        $st = $pdo->prepare('SELECT estado FROM incidencias WHERE id = ? FOR UPDATE');
        $st->execute([$id]);
        $anterior = $st->fetchColumn();
        if ($anterior === false) {
            throw new RuntimeException('La incidencia no existe.');
        }

        $pdo->prepare('UPDATE incidencias SET estado = ? WHERE id = ?')->execute([$nuevo, $id]);
        $pdo->prepare(
            'INSERT INTO historial (incidencia_id, usuario_id, estado_anterior, estado_nuevo, comentario)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$id, $usuarioId, $anterior, $nuevo, $comentario]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Borra una incidencia. Su historial se borra solo gracias al ON DELETE CASCADE.
 *
 * @param int $id Id de la incidencia.
 * @return void
 */
function borrarIncidencia(int $id): void
{
    bd()->prepare('DELETE FROM incidencias WHERE id = ?')->execute([$id]);
}
