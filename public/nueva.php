<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/nueva.php
 * ============================================================================
 *  Formulario para crear una nueva incidencia: valida los campos en servidor
 *  mediante listas blancas, protege con CSRF, guarda en MySQL y aplica PRG.
 */
require_once __DIR__ ."/../config.php";
require_once __DIR__ ."/../includes/funciones.php";
require_once __DIR__ ."/../includes/db.php";
// Solo con sesión iniciada: el autor de la incidencia es siempre el usuario de la sesión
$usuario = exigirSesion();
// Los equipos del desplegable salen de la base de datos
$equipos = listarEquipos();
$errores = [];
$datos = [
    'equipo_id' => '',
    'categoria' => '',
    'prioridad' => '', 
    'descripcion' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf(leerPost('csrf'));
    $datos['equipo_id']   = leerPost('equipo_id');
    $datos['categoria']   = leerPost('categoria');
    $datos['prioridad']   = leerPost('prioridad');
    $datos['descripcion'] = leerPost('descripcion');
    $equipoId = filter_var($datos['equipo_id'], FILTER_VALIDATE_INT);
    if ($equipoId === false || !in_array($equipoId, array_map('intval', array_column($equipos, 'id')), true)) {
        $errores['equipo_id'] = 'Selecciona un equipo válido.';
    }
    if (!enListaBlanca($datos['categoria'], CATEGORIAS)){
        $errores['categoria'] = 'Selecciona una categoría válida.';
    }
    if (!enListaBlanca($datos['prioridad'], PRIORIDADES)){
        $errores['prioridad'] = 'Selecciona una prioridad válida.';
    }
    if (!longitudValida($datos['descripcion'],10, 500)){
        $errores['descripcion'] = 'La descripción debe tener entre 10 y 500 caracteres.';
    }
    if(empty($errores)){
        // Todo validado: se guarda con sentencia preparada (estado inicial 'abierta' lo pone la BD)
        crearIncidencia($equipoId, $usuario['id'], $datos['categoria'], $datos['prioridad'], $datos['descripcion']);
        ponerFlash('Incidencia creada correctamente.');
        redirigir('incidencias.php');
    }
}
$titulo = 'Nueva incidencia';
$scripts = ['js/nueva.js'];
require_once __DIR__ . '/../includes/cabecera.php';
?>

<h1>Nueva incidencia</h1>

<!-- Se envía por POST para proteger los datos y validar siempre en servidor aunque quiten atributos HTML -->
<form method="post" action="">
    <!-- Token único de sesión: impide que otra web envíe el formulario en nombre del usuario (CSRF) -->
    <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

    <!-- Desplegable dinámico: conserva el equipo elegido tras un fallo comparando su id con $datos -->
    <div>
        <label for="equipo_id">Equipo:</label><br>
        <select id="equipo_id" name="equipo_id">
            <option value="">-- Selecciona un equipo --</option>
            <?php foreach ($equipos as $eq): ?>
                <option value="<?= e($eq['id']) ?>" <?= ((string) $eq['id'] === $datos['equipo_id']) ? 'selected' : '' ?>>
                    <?= e($eq['codigo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errores['equipo_id'])): ?>
            <p class="error"><?= e($errores['equipo_id']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Lee las opciones desde config.php para garantizar que HTML y validación usen la misma lista blanca -->
    <div>
        <label for="categoria">Categoría:</label><br>
        <select id="categoria" name="categoria">
            <option value="">-- Selecciona una categoría --</option>
            <?php foreach (CATEGORIAS as $cat): ?>
                <option value="<?= e($cat) ?>" <?= ($cat === $datos['categoria']) ? 'selected' : '' ?>>
                    <?= e(ucfirst($cat)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errores['categoria'])): ?>
            <p class="error"><?= e($errores['categoria']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Agrupa los botones radio en fieldset/legend por accesibilidad y marca 'checked' si hubo error al validar -->
    <fieldset>
        <legend>Prioridad</legend>
        <?php foreach (PRIORIDADES as $prio): ?>
            <label>
                <input
                    type="radio"
                    name="prioridad"
                    value="<?= e($prio) ?>"
                    <?= ($prio === $datos['prioridad']) ? 'checked' : '' ?>
                >
                <?= e(ucfirst($prio)) ?>
            </label>
        <?php endforeach; ?>
        <?php if (isset($errores['prioridad'])): ?>
            <p class="error"><?= e($errores['prioridad']) ?></p>
        <?php endif; ?>
    </fieldset>

    <!-- Recupera la descripción escapada con e() para evitar XSS al recargar el formulario con errores -->
    <div>
        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="4" cols="40" aria-describedby="contador-caracteres"><?= e($datos['descripcion']) ?></textarea>
        <!-- Enlazado con aria-describedby para que js/nueva.js actualice los caracteres restantes sin romper la vista sin JS -->
        <p id="contador-caracteres"></p>
        <?php if (isset($errores['descripcion'])): ?>
            <p class="error"><?= e($errores['descripcion']) ?></p>
        <?php endif; ?>
    </div>

    <div class="acciones-formulario">
        <button type="submit">Enviar incidencia</button>
        <a href="incidencias.php" class="boton boton-secundario">Cancelar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>


