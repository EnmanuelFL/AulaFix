/**
 * ============================================================================
 *  AulaFix · public/js/vistas.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Hito 1 & Entrega final (UT4, UT5)
 *  RA:     0612-RA4 (resumen con reduce, funciones puras)
 *          0612-RA6 (construcción con DOM, textContent, DocumentFragment,
 *          sin innerHTML con datos, aria-live, aria-sort, aria-selected)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Módulo encargado de generar, actualizar y pintar todos los elementos visuales
 *  del panel de incidencias en el navegador. Contiene las funciones puras de
 *  cálculo y las funciones de manipulación del DOM.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero y JSDoc en cada función (@param, @returns).
 *
 *  1. CÁLCULO DE RESUMEN (UT4 - RA4):
 *  [ ] Función `calcularResumen(listaIncidencias)` que devuelva un objeto con:
 *        { abierta: N, en_curso: N, resuelta: N, urgentes: N }
 *  [ ] REGLA ESTRICTA: debe calcularse recorriendo la lista UNA SOLA VEZ
 *      usando `reduce` (mirad el ejemplo de la página 8 del PDF).
 *
 *  2. PINTADO DEL RESUMEN:
 *  [ ] Función que actualiza las 4 tarjetas con los contadores calculados.
 *
 *  3. PINTADO DE LA TABLA (UT5 - RA6):
 *  [ ] Función `renderizarTabla(listaIncidencias, tbodyElement)`:
 *        · REGLA DE SEGURIDAD ESTRICTA: insertar SIEMPRE los datos con
 *          `textContent` o `document.createTextNode()`. PROHIBIDO el uso de
 *          `innerHTML` con datos de usuarios para evitar inyecciones XSS.
 *        · RENDIMIENTO (Sobresaliente RA6): acumular las filas en un
 *          `document.createDocumentFragment()` antes de insertarlo en el DOM.
 *        · Resaltar las incidencias urgentes asignando la clase CSS `.urgente` al <tr>.
 *        · Enlace en cada fila al detalle: `incidencia.php?id=${incidencia.id}`.
 *        · Mostrar la fecha relativa («hace 2 días») usando el módulo fechas.js,
 *          y la fecha completa en el atributo `title` para tooltip al pasar el ratón.
 *
 *  4. ACCESIBILIDAD Y CONTADORES:
 *  [ ] Actualizar el contador accesible: «Mostrando X de Y incidencias».
 *        · El contenedor debe tener `aria-live="polite"` para que los lectores
 *          de pantalla lo anuncien al cambiar filtros.
 *  [ ] Actualizar el atributo `aria-selected="true"` en la pestaña activa
 *      y `aria-selected="false"` en las demás.
 *  [ ] Actualizar `aria-sort="ascending"` / `aria-sort="descending"` / `none`
 *      en la cabecera de la tabla que esté actualmente ordenada.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Para vaciar el <tbody> antes de repintar: `tbody.replaceChildren()`
 *    o `tbody.textContent = ''`.
 *  - ¿Por qué DocumentFragment? (Ver apuntes de Unidad 5 DOM):
 *    "Es como meter los ladrillos en un camión y descargarlos todos a la vez,
 *    evitando que el navegador recalcule el diseño 2000 veces (reflow)".
 *  - En la rúbrica de 0612-RA6: usar `innerHTML` con datos de usuarios baja
 *    la nota directamente a "Insuficiente". Probad escribir un título como
 *    <script>alert(1)</script> y comprobad que sale como texto plano.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Array.prototype.reduce(): https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Array/reduce
 *  - Document.createDocumentFragment(): https://developer.mozilla.org/es/docs/Web/API/Document/createDocumentFragment
 *  - Node.textContent vs Element.innerHTML
 *  - Element.replaceChildren(): https://developer.mozilla.org/es/docs/Web/API/Element/replaceChildren
 *  - ARIA: aria-live, aria-selected, aria-sort (MDN)
 */

