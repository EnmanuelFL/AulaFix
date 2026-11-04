<?php
/*
 * ============================================================================
 *  AulaFix · public/nueva.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Hito 1 (formulario validado + PRG + CSRF, SIN guardar en la BD)
 *          → Entrega final (se guarda en MySQL)
 *  RA:     0613-RA3 (validación, errores por campo, conservar lo escrito,
 *          listas blancas, PRG, CSRF) · 0613-RA6 (INSERT preparado)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El formulario para avisar de una incidencia nueva. Muestra el formulario
 *  (GET) y lo procesa (POST). Lo pueden usar alumnos y profesores.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado, apartado 3.2)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Solo con sesión iniciada.
 *  [ ] Campos:
 *        · equipo      → desplegable (<select>) cargado desde la base de datos
 *                        (en el Hito 1, desde un array).
 *        · categoria   → hardware · software · red · otro
 *        · prioridad   → BOTONES DE OPCIÓN (radio): baja · media · alta
 *        · descripcion → <textarea>, de 10 a 500 caracteres.
 *  [ ] Validación EN EL SERVIDOR con LISTAS BLANCAS (equipo existente,
 *      categoría y prioridad permitidas, longitud de la descripción).
 *  [ ] Errores JUNTO A CADA CAMPO y se conserva lo escrito (incluido el
 *      option seleccionado y el radio marcado).
 *  [ ] Si todo está bien: guardar → mensaje flash → redirigir al listado (PRG).
 *  [ ] POST + token CSRF. Salida escapada con e().
 *  [ ] Cargar js/nueva.js como módulo (contador de caracteres + borrador).
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - El autor de la incidencia es el usuario de la SESIÓN, nunca un campo del
 *    formulario. ¿Por qué? (Cualquiera puede editar un campo oculto.)
 *  - El estado inicial siempre es 'abierta': tampoco viene del formulario.
 *  - Lista blanca del equipo: el id que llega por POST tiene que existir en la
 *    lista de equipos. ¿Os sirve array_column() para sacar los ids?
 *  - Conservar lo escrito en un <select>: hay que poner "selected" en la opción
 *    que coincide. En los radio: "checked". Escribid una pequeña función o
 *    usad un operador ternario dentro del HTML.
 *  - Los valores de categoría y prioridad para pintar el formulario salen de
 *    config.php (la MISMA lista que usáis para validar).
 *  - Agrupad los radio en <fieldset> con <legend> (accesibilidad).
 *  - Poned un elemento para el contador de caracteres con un id acordado con
 *    el compañero del 0612 (lo rellenará nueva.js).
 *  - Probad a quitar el atributo required con el inspector y enviar: el
 *    servidor TIENE que seguir rechazando el formulario.
 *  - PRG: después de un envío correcto, pulsad F5 en el listado. ¿Se repite la
 *    incidencia? Si no, el PRG funciona (Sobresaliente RA3).
 *  - Prueba del anexo: escribid «<b>hola</b>» en la descripción. En el
 *    listado debe verse el texto tal cual, no en negrita.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Elementos de formulario: select/option, input type="radio", textarea,
 *    fieldset/legend (MDN)
 *  - in_array con modo estricto, array_column
 *  - filter_var con FILTER_VALIDATE_INT (el equipo llega como texto)
 *  - mb_strlen para la longitud de la descripción
 *  - Mensajes flash en $_SESSION
 */

