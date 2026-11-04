<?php
/*
 * ============================================================================
 *  AulaFix · includes/pie.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor (con un detalle del 0612: carga de los módulos JS)
 *  HITO:   Hito 1
 *  RA:     0613-RA2 (cabecera y pie comunes con include) · 0612-RA1 (script
 *          cargado como módulo)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El final de TODAS las páginas: cierra el <main>, pinta el <footer> y
 *  cierra </body> y </html>. Es lo último que incluye cada página.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Cerrar las etiquetas que abrió cabecera.php (main, body, html).
 *  [ ] Pie de página sencillo (nombre de la app, curso, autores...).
 *  [ ] Cargar el JavaScript que necesite cada página como MÓDULO:
 *        incidencias.php → js/main.js
 *        nueva.php       → js/nueva.js
 *      (el resto de páginas no necesitan JavaScript).
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - ¿Cómo sabe el pie qué script cargar? Una idea: la página define antes
 *    una variable (por ejemplo un array $scripts) y el pie la recorre. Otra:
 *    cada página escribe su propia etiqueta <script> antes de incluir el pie.
 *    Elegid una y comentad por qué.
 *  - Los scripts con type="module" se ejecutan en modo diferido (como defer):
 *    esperan a que el HTML esté leído. ¿Hace falta entonces DOMContentLoaded?
 *  - En incidencias.php, la etiqueta <script type="application/json"> con los
 *    datos tiene que estar en la página ANTES de que main.js intente leerla.
 *  - Si escribís la ruta del script desde una variable, pasadla también por e().
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - <script type="module">: https://developer.mozilla.org/es/docs/Web/JavaScript/Guide/Modules
 *  - Diferencias entre script normal, defer, async y module
 *  - Elemento <footer> (MDN)
 */

