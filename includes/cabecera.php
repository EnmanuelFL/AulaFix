<?php
/*
 * ============================================================================
 *  AulaFix · includes/cabecera.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Hito 1 (cabecera común con include) · Entrega final (menú según sesión)
 *  RA:     0613-RA2 (cabecera y pie comunes con include, salida escapada)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El principio de TODAS las páginas: el <!DOCTYPE>, el <head>, el menú de
 *  navegación y la apertura del <main>. Las páginas lo cargan con include
 *  (o require) DESPUÉS de haber hecho toda su lógica.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] HTML5 válido: <!DOCTYPE html>, <html lang="es">, <meta charset="UTF-8">,
 *      <meta name="viewport" ...>.
 *  [ ] <title> distinto en cada página (la página define una variable antes
 *      de incluir este fichero, por ejemplo $titulo). Escapado con e().
 *  [ ] Enlace a css/estilos.css.
 *  [ ] Menú de navegación:
 *        · Sin sesión → enlaces a «Iniciar sesión» y «Registrarse».
 *        · Con sesión → nombre del usuario y su rol, enlaces a «Incidencias»
 *          y «Nueva incidencia», y el botón «Cerrar sesión».
 *  [ ] «Cerrar sesión» NO es un enlace: es un pequeño FORMULARIO POST con el
 *      token CSRF (lo pide el enunciado para logout.php).
 *  [ ] Mostrar el mensaje flash si lo hay (p. ej. «Incidencia creada»).
 *  [ ] TODO lo que venga de variables o de la sesión, pasado por e().
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Este fichero es sobre todo HTML con trocitos de PHP. Usad la sintaxis
 *    alternativa (if: ... endif;) y la etiqueta corta de impresión para que
 *    se lea bien. Mirad el ejemplo de formulario de los apuntes.
 *  - Las rutas a css/ y js/ son relativas a public/, porque la página que
 *    se ejecuta está en public/, aunque este fichero viva en includes/.
 *  - ¿Qué pasa si un usuario se registra con el nombre <script>alert(1)</script>
 *    y se muestra en el menú sin e()? Probadlo cuando lo tengáis.
 *  - Usad etiquetas semánticas: <header>, <nav>, <main>. Ayudan en la
 *    accesibilidad y en la nota.
 *  - Para marcar el enlace de la página actual podéis usar aria-current="page".
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - include / require / require_once: https://www.php.net/manual/es/function.include.php
 *  - Sintaxis alternativa de estructuras de control:
 *    https://www.php.net/manual/es/control-structures.alternative-syntax.php
 *  - Etiqueta corta de impresión (echo corto) en PHP
 *  - Elementos semánticos HTML5 y aria-current (MDN)
 */

