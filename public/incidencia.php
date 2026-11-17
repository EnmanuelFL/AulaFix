<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/incidencia.php   (se usa como incidencia.php?id=3)
 * ============================================================================
 *  Detalle de una incidencia con su historial. El profesorado puede cambiar el estado dejando
 *  un comentario (UPDATE + historial en una transacción) y se puede borrar según los permisos.
 *  Los permisos se comprueban en el servidor: si alguien envía el formulario a mano sin permiso, 403.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';

$usuario = exigirSesion();

// El id llega como texto en la URL: se valida que sea un entero positivo
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(404);
    exit('Incidencia no encontrada.');
}

$incidencia = buscarIncidencia($id);
if ($incidencia === null) {
    http_response_code(404);
    exit('Incidencia no encontrada.');
}

$errores = [];
$datos = ['estado' => '', 'comentario' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf(leerPost('csrf'));

    // Un campo oculto «accion» indica cuál de los dos formularios de la página se ha enviado
    $accion = leerPost('accion');

    if ($accion === 'estado') {
        // Solo el profesorado puede cambiar el estado (se comprueba aquí aunque el botón no se vea)
        if ($usuario['rol'] !== 'profesor') {
            http_response_code(403);
            exit('Acceso denegado: solo el profesorado puede cambiar el estado.');
        }

        $datos['estado']     = leerPost('estado');
        $datos['comentario'] = leerPost('comentario');

        if (!enListaBlanca($datos['estado'], ESTADOS)) {
            $errores['estado'] = 'Elige un estado válido.';
        } elseif ($datos['estado'] === $incidencia['estado']) {
            $errores['estado'] = 'La incidencia ya tiene ese estado.';
        }
        if (!longitudValida($datos['comentario'], 3, 500)) {
            $errores['comentario'] = 'El comentario debe tener entre 3 y 500 caracteres.';
        }

        if (empty($errores)) {
            cambiarEstado($id, $datos['estado'], $datos['comentario'], $usuario['id']);
            ponerFlash('Estado actualizado correctamente.');
            redirigir('incidencia.php?id=' . $id);
        }
    } elseif ($accion === 'borrar') {
        // La misma regla (puedeBorrar) decide si se pinta el botón y si se acepta la petición
        if (!puedeBorrar($incidencia, $usuario)) {
            http_response_code(403);
            exit('Acceso denegado: no puedes borrar esta incidencia.');
        }

        borrarIncidencia($id);
        ponerFlash('Incidencia eliminada.');
        redirigir('incidencias.php');
    } else {
        http_response_code(400);
        exit('Acción no válida.');
    }
}

$historial = historialDe($id);

$titulo = 'Incidencia #' . $id;
$scripts = [];
require_once __DIR__ . '/../includes/cabecera.php';
?>

<h1>Incidencia #<?= e($incidencia['id']) ?></h1>

<dl>
    <dt>Equipo</dt>      <dd><?= e($incidencia['equipo']) ?></dd>
    <dt>Categoría</dt>   <dd><?= e($incidencia['categoria']) ?></dd>
    <dt>Prioridad</dt>   <dd><?= e($incidencia['prioridad']) ?></dd>
    <dt>Estado</dt>      <dd><?= e(str_replace('_', ' ', $incidencia['estado'])) ?></dd>
    <dt>Autor</dt>       <dd><?= e($incidencia['autor']) ?></dd>
    <dt>Creada</dt>      <dd><?= e(formatearFecha($incidencia['creada_en'])) ?></dd>
    <dt>Descripción</dt> <dd><?= e($incidencia['descripcion']) ?></dd>
</dl>

<h2>Historial</h2>
<?php if (empty($historial)): ?>
    <p>Todavía no hay cambios de estado.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>Fecha</th><th>Quién</th><th>Cambio</th><th>Comentario</th></tr>
        </thead>
        <tbody>
            <?php foreach ($historial as $h): ?>
                <tr>
                    <td><?= e(formatearFecha($h['creado_en'])) ?></td>
                    <td><?= e($h['autor']) ?></td>
                    <td><?= e(str_replace('_', ' ', $h['estado_anterior'])) ?> → <?= e(str_replace('_', ' ', $h['estado_nuevo'])) ?></td>
                    <td><?= e($h['comentario']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($usuario['rol'] === 'profesor'): ?>
    <h2>Cambiar estado</h2>
    <form method="post" action="incidencia.php?id=<?= e($incidencia['id']) ?>">
        <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
        <input type="hidden" name="accion" value="estado">

        <div>
            <label for="estado">Nuevo estado:</label><br>
            <select id="estado" name="estado">
                <option value="">-- Selecciona un estado --</option>
                <?php foreach (ESTADOS as $est): ?>
                    <option value="<?= e($est) ?>" <?= ($est === $datos['estado']) ? 'selected' : '' ?>>
                        <?= e(str_replace('_', ' ', $est)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['estado'])): ?>
                <p class="error"><?= e($errores['estado']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="comentario">Comentario:</label><br>
            <textarea id="comentario" name="comentario" rows="3" cols="40"><?= e($datos['comentario']) ?></textarea>
            <?php if (isset($errores['comentario'])): ?>
                <p class="error"><?= e($errores['comentario']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Guardar cambio</button>
    </form>
<?php endif; ?>

<?php if (puedeBorrar($incidencia, $usuario)): ?>
    <h2>Borrar incidencia</h2>
    <form method="post" action="incidencia.php?id=<?= e($incidencia['id']) ?>">
        <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
        <input type="hidden" name="accion" value="borrar">
        <button type="submit">Borrar incidencia</button>
    </form>
<?php endif; ?>

<p><a href="incidencias.php">← Volver al listado</a></p>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>
