<?php
/*
 * ============================================================================
 *  AulaFix · public/incidencias.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor (y aquí se conecta con el 0612 · Cliente)
 *  HITO:   Hito 1 (datos de ejemplo en un ARRAY de PHP) → Entrega final (datos
 *          de MySQL)
 *  RA:     0613-RA2 (HTML generado con PHP, e(), JSON seguro)
 *          0613-RA4 (exige sesión) · 0613-RA6 (listado desde la BD)
 *          0612-RA1 (los datos llegan al cliente en JSON, mejora progresiva)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El listado de todas las incidencias. Hace DOS cosas a la vez:
 *    1. Pinta una tabla HTML normal generada con PHP (funciona sin JavaScript).
 *    2. Mete las mismas incidencias en la página en formato JSON para que el
 *       panel interactivo (js/main.js) las lea y sustituya la tabla.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado, apartados 3.2, 3.3 y 3.4)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Solo con sesión iniciada (alumno o profesor). Sin sesión → login.php.
 *  [ ] Tabla HTML con las incidencias generada en PHP (equipo, categoría,
 *      prioridad, descripción, estado, autor, fecha) y un enlace al detalle
 *      incidencia.php?id=...
 *  [ ] Toda la salida escapada con e().
 *  [ ] Los datos en JSON dentro de:
 *        <script id="datos-incidencias" type="application/json"> ... </script>
 *      generado con json_encode() y las opciones seguras JSON_HEX_*.
 *  [ ] Cargar js/main.js como módulo (type="module").
 *  [ ] Mostrar el mensaje flash (por ejemplo, tras crear una incidencia).
 *  [ ] Si JavaScript está desactivado, se sigue viendo la tabla de PHP.
 *
 *  «CONTRATO» de cada incidencia en el JSON (NO se cambia sin avisar al
 *  compañero del 0612):
 *    { "id": 3, "equipo": "PROY-01", "categoria": "hardware",
 *      "prioridad": "alta", "descripcion": "El proyector se apaga solo.",
 *      "estado": "abierta", "autor": "Lucía Alumna",
 *      "creada_en": "2026-11-02T18:30:00+01:00" }
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - HITO 1: cread un array de PHP con 5-6 incidencias que sigan EXACTAMENTE
 *    el contrato. Así el compañero del 0612 puede empezar el panel sin
 *    esperar a la base de datos. En la entrega final lo cambiáis por
 *    listarIncidencias() de db.php y la página casi no cambia.
 *  - Dejad en el HTML los CONTENEDORES vacíos del panel (resumen, pestañas,
 *    filtros, tabla, contador) ocultos con el atributo hidden, y que el JS
 *    los muestre. Así, sin JS, el usuario solo ve la tabla de PHP. Poneos de
 *    acuerdo con el 0612 en los id de cada contenedor.
 *  - Accesibilidad que pide la rúbrica del 0612 (se puede preparar ya en el HTML):
 *      · pestañas → role="tablist" / role="tab" y aria-selected
 *      · cabeceras ordenables → aria-sort
 *      · contador «Mostrando X de Y» → aria-live="polite"
 *  - PREGUNTA DE DEFENSA (Sobresaliente RA2 0613): ¿por qué el JSON lleva
 *    JSON_HEX_TAG? ¿Qué pasaría si una descripción contiene "</script>"?
 *  - Comprobad en el navegador con «Ver código fuente» que el JSON aparece
 *    bien y que los caracteres < > & ' " salen escapados (\u003C, ...).
 *  - Para probar sin JS: DevTools → Ctrl+Shift+P → «Disable JavaScript».
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - json_encode y opciones JSON_HEX_*: https://www.php.net/manual/es/json.constants.php
 *  - <script type="application/json"> como "isla de datos"
 *  - foreach con sintaxis alternativa para generar las filas de la tabla
 *  - Atributo global hidden (MDN)
 *  - Mejora progresiva (progressive enhancement)
 *  - Roles ARIA: tablist, tab · aria-sort · aria-live (MDN)
 */

