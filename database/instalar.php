<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · database/instalar.php
 * ============================================================================
 *  Script de CONSOLA que crea la base de datos, ejecuta esquema.sql y carga datos de ejemplo.
 *  Uso (desde la raíz del proyecto):   php database/instalar.php
 *  OJO: borra las tablas existentes y las vuelve a crear (solo para desarrollo).
 */

// Solo se puede lanzar desde la consola: si alguien lo pidiera por el navegador, se corta
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se ejecuta desde la consola.');
}

require_once __DIR__ . '/../config.php';

// El nombre de la BD va dentro de un identificador SQL (no se puede preparar), así que se valida
if (!preg_match('/^[A-Za-z0-9_]+$/', DB_NAME)) {
    exit("Nombre de base de datos no válido en la configuración.\n");
}

try {
    // Primero se conecta SIN base de datos, para poder crearla si todavía no existe
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=%s', DB_HOST, DB_PORT, DB_CHARSET),
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE `' . DB_NAME . '`');

    // Ejecuta esquema.sql: se quitan las líneas de comentario y se lanza cada sentencia por separado
    $sql = file_get_contents(__DIR__ . '/esquema.sql');
    $sql = preg_replace('/^\s*--.*$/m', '', (string) $sql);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $sentencia) {
        $pdo->exec($sentencia);
    }
    echo "Tablas creadas.\n";

    // Todos los INSERT van en una transacción: si algo falla, no queda la BD a medias
    $pdo->beginTransaction();

    // --- Usuarios (la contraseña se guarda con Argon2id, nunca en claro) ---
    $claveDemo = 'AulaFix2026';
    $usuarios = [
        ['Pablo Profesor', 'profesor@aulafix.local', 'profesor'],
        ['Lucía Alumna',   'lucia@aulafix.local',    'alumno'],
        ['Marcos Alumno',  'marcos@aulafix.local',   'alumno'],
    ];
    $stUsuario = $pdo->prepare('INSERT INTO usuarios (nombre, email, clave_hash, rol) VALUES (?, ?, ?, ?)');
    $idUsuario = []; // email => id
    foreach ($usuarios as [$nombre, $email, $rol]) {
        $stUsuario->execute([$nombre, $email, password_hash($claveDemo, PASSWORD_ARGON2ID), $rol]);
        $idUsuario[$email] = (int) $pdo->lastInsertId();
    }

    // --- Equipos ---
    $equipos = [
        ['PC-01',   'Ordenador del puesto 1'],
        ['PC-02',   'Ordenador del puesto 2'],
        ['PC-03',   'Ordenador del puesto 3'],
        ['PROY-01', 'Proyector del aula'],
        ['IMP-01',  'Impresora del pasillo'],
        ['RED-01',  'Switch de red del aula'],
    ];
    $stEquipo = $pdo->prepare('INSERT INTO equipos (codigo, descripcion) VALUES (?, ?)');
    $idEquipo = []; // código => id
    foreach ($equipos as [$codigo, $descripcion]) {
        $stEquipo->execute([$codigo, $descripcion]);
        $idEquipo[$codigo] = (int) $pdo->lastInsertId();
    }

    // --- Incidencias de ejemplo con fechas variadas (para probar el «hace X días» del panel) ---
    // 'cambios' = cambios de estado que hizo el profesor: [nuevo estado, comentario, cuánto después de crearla]
    $incidencias = [
        ['PROY-01', 'lucia@aulafix.local',  'hardware', 'alta',  'El proyector se apaga solo a los diez minutos.', '-2 days', []],
        ['PC-02',   'marcos@aulafix.local', 'software', 'media', 'No abre el editor de código instalado.', '-3 days', [
            ['en_curso', 'Lo reviso esta tarde en clase.', '+3 hours'],
        ]],
        ['IMP-01',  'lucia@aulafix.local',  'hardware', 'baja',  'La impresora se ha quedado sin tóner.', '-6 days', [
            ['en_curso', 'Pedido el tóner nuevo.', '+2 hours'],
            ['resuelta', 'Tóner cambiado y probado.', '+1 day'],
        ]],
        ['RED-01',  'marcos@aulafix.local', 'red',      'alta',  'No hay conexión a internet en todo el aula.', '-1 day', [
            ['en_curso', 'Avisado el técnico de red.', '+1 hour'],
        ]],
        ['PC-01',   'lucia@aulafix.local',  'software', 'baja',  'Pide actualizar el sistema operativo.', '-5 hours', []],
        ['PC-03',   'marcos@aulafix.local', 'otro',     'media', 'El ratón no responde y tiene el cable roto.', '-8 days', [
            ['en_curso', 'Pedido un ratón nuevo.', '+1 day'],
            ['resuelta', 'Ratón sustituido.', '+3 days'],
        ]],
    ];

    $ahora = new DateTimeImmutable('now');
    $formato = 'Y-m-d H:i:s';
    $stIncidencia = $pdo->prepare(
        'INSERT INTO incidencias (equipo_id, usuario_id, categoria, prioridad, descripcion, estado, creada_en, actualizada_en)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stHistorial = $pdo->prepare(
        'INSERT INTO historial (incidencia_id, usuario_id, estado_anterior, estado_nuevo, comentario, creado_en)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    foreach ($incidencias as [$codigoEquipo, $emailAutor, $categoria, $prioridad, $descripcion, $hace, $cambios]) {
        $creada = $ahora->modify($hace);

        // Se calcula primero el estado final y la fecha del último cambio
        $estado = 'abierta';
        $actualizada = $creada;
        $filasHistorial = [];
        foreach ($cambios as [$nuevoEstado, $comentario, $despues]) {
            $fechaCambio = $creada->modify($despues);
            $filasHistorial[] = [$estado, $nuevoEstado, $comentario, $fechaCambio->format($formato)];
            $estado = $nuevoEstado;
            $actualizada = $fechaCambio;
        }

        $stIncidencia->execute([
            $idEquipo[$codigoEquipo], $idUsuario[$emailAutor], $categoria, $prioridad, $descripcion,
            $estado, $creada->format($formato), $actualizada->format($formato),
        ]);
        $idIncidencia = (int) $pdo->lastInsertId();

        foreach ($filasHistorial as [$anterior, $nuevo, $comentario, $fecha]) {
            $stHistorial->execute([$idIncidencia, $idUsuario['profesor@aulafix.local'], $anterior, $nuevo, $comentario, $fecha]);
        }
    }

    $pdo->commit();

    echo "Datos de ejemplo cargados: " . count($usuarios) . " usuarios, " . count($equipos) . " equipos, "
        . count($incidencias) . " incidencias.\n";
    echo "Contraseña de todos los usuarios de prueba: {$claveDemo}\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'Error durante la instalación: ' . $e->getMessage() . "\n");
    exit(1);
}
