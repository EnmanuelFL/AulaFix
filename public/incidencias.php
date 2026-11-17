<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/incidencias.php
 * ============================================================================
 *  Listado de incidencias: genera la tabla HTML de respaldo en PHP y el bloque
 *  JSON seguro para que el panel interactivo del cliente (js/main.js) lo lea.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';

// Solo pueden ver el listado los usuarios con sesión (si no, redirige a login.php)
exigirSesion();

// Las incidencias salen de MySQL con el formato exacto del «contrato» JSON del cliente
$incidencias = listarIncidencias();

$titulo = 'Incidencias';
$scripts = ['js/main.js'];
require_once __DIR__ . '/../includes/cabecera.php';
?>
<h1>Tablón de incidencias</h1>

<!-- Tarjetas de resumen: ocultas sin JS; main.js las rellena y les quita hidden -->
<section id="resumen-incidencias" hidden>
    <article class="tarjeta-resumen">
        <h3>Abiertas</h3>
        <p id="contador-abiertas">0</p>
    </article>
    <article class="tarjeta-resumen">
        <h3>En curso</h3>
        <p id="contador-en-curso">0</p>
    </article>
    <article class="tarjeta-resumen">
        <h3>Resueltas</h3>
        <p id="contador-resueltas">0</p>
    </article>
    <article class="tarjeta-resumen">
        <h3>Urgentes</h3>
        <p id="contador-urgentes">0</p>
    </article>
</section>

<!-- Panel interactivo del cliente (0612): oculto por defecto para garantizar mejora progresiva -->
<section id="panel-interactivo" hidden>
    <div id="pestanas-incidencias" role="tablist" aria-label="Filtrar por estado">
        <button type="button" role="tab" data-pestana="todas" aria-selected="true">Todas</button>
        <button type="button" role="tab" data-pestana="abierta" aria-selected="false">Abiertas</button>
        <button type="button" role="tab" data-pestana="en_curso" aria-selected="false">En curso</button>
        <button type="button" role="tab" data-pestana="resuelta" aria-selected="false">Resueltas</button>
    </div>

    <div class="controles-panel">
        <input type="search" id="buscador" placeholder="Buscar por equipo, descripción o autor..." aria-label="Buscar incidencias">
        <select id="filtro-prioridad" aria-label="Filtrar por prioridad">
            <option value="">Todas las prioridades</option>
            <?php foreach (PRIORIDADES as $prio): ?>
                <option value="<?= e($prio) ?>"><?= e(ucfirst($prio)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <p id="contador-resultados" aria-live="polite"></p>

    <table id="tabla-incidencias">
        <thead>
            <tr>
                <th>ID</th>
                <th data-columna="equipo" aria-sort="none">Equipo</th>
                <th>Categoría</th>
                <th data-columna="prioridad" aria-sort="none">Prioridad</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Autor</th>
                <th data-columna="fecha" aria-sort="none">Fecha</th>
            </tr>
        </thead>
        <tbody id="cuerpo-incidencias"></tbody>
    </table>
</section>

<!-- Listado estático de respaldo en PHP: siempre visible si JavaScript está desactivado -->
<section id="listado-servidor">
    <table id="tabla-php">
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Categoría</th>
                <th>Prioridad</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Autor</th>
                <th>Fecha</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incidencias as $inc): ?>
                <tr>
                    <td><?= e($inc['equipo']) ?></td>
                    <td><?= e($inc['categoria']) ?></td>
                    <td><?= e($inc['prioridad']) ?></td>
                    <td><?= e($inc['descripcion']) ?></td>
                    <td><?= e($inc['estado']) ?></td>
                    <td><?= e($inc['autor']) ?></td>
                    <td><?= e(formatearFecha($inc['creada_en'])) ?></td>
                    <td><a href="incidencia.php?id=<?= e($inc['id']) ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<script id="datos-incidencias" type="application/json"><?= jsonSeguro($incidencias) ?></script>
<?php require __DIR__ .'/../includes/pie.php'?>

