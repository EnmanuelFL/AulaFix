<?php
/*
 * ============================================================================
 *  AulaFix · public/index.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Hito 1
 *  RA:     0613-RA1 (organización de las páginas en public/)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  La puerta de entrada. NO lo pide el enunciado, pero es recomendable: al
 *  abrir http://localhost:8000/ el servidor busca index.php, y si no existe
 *  da un error 404. Esta página no pinta nada: solo REDIRIGE.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Si hay sesión iniciada → redirigir a incidencias.php.
 *  [ ] Si no hay sesión        → redirigir a login.php.
 *  (En el Hito 1, sin login todavía, podéis redirigir siempre a incidencias.php.)
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Reutilizad las funciones de includes/funciones.php (arrancar sesión,
 *    usuarioActual(), redirigir()). Aquí deberían bastar 3 o 4 líneas.
 *  - Para incluir ficheros que están fuera de public/ usad rutas con __DIR__
 *    (por ejemplo __DIR__ . '/../includes/...'). ¿Por qué es más seguro que
 *    una ruta relativa sin __DIR__?
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Constante mágica __DIR__: https://www.php.net/manual/es/language.constants.magic.php
 *  - Servidor web integrado de PHP y el parámetro -t (document root):
 *    https://www.php.net/manual/es/features.commandline.webserver.php
 */

