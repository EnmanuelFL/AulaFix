/**
 * ============================================================================
 *  AulaFix · public/js/fechas.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Hito 1 (fecha en formato español y «hace X días» con Date e Intl)
 *  RA:     0612-RA3 (objetos predefinidos: Date, Intl.DateTimeFormat,
 *          Intl.RelativeTimeFormat)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Módulo auxiliar exportable con funciones de utilidad para el tratamiento,
 *  cálculo y formateo de fechas tanto en modo absoluto (fecha completa legible)
 *  como en modo relativo («hace 2 horas», «hace 3 días»).
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero explicando qué hace.
 *  [ ] Formato de módulo ES (export function ...).
 *  [ ] Toda función documentada con JSDoc (@param, @returns).
 *  [ ] Función para fecha completa en español (por ejemplo: "2 de noviembre de 2026, 18:30"):
 *        · Debe usar `Intl.DateTimeFormat` o `toLocaleDateString('es-ES', ...)`.
 *  [ ] Función para tiempo relativo («hace X días», «hace X horas», «hace unos segundos»):
 *        · Debe usar `Intl.RelativeTimeFormat` o cálculo de diferencia temporal con `Date`.
 *  [ ] Manejo de casos límite: fechas inválidas o cadenas mal formadas (no romper la app).
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Para calcular la diferencia entre la fecha de creación y el momento actual:
 *      const ahora = new Date();
 *      const fechaIncidencia = new Date(isoString);
 *      const diferenciaSegundos = Math.round((fechaIncidencia - ahora) / 1000);
 *  - Con `Intl.RelativeTimeFormat('es', { numeric: 'auto' })`:
 *      const rtf = new Intl.RelativeTimeFormat('es', { numeric: 'auto' });
 *      rtf.format(-2, 'day'); // Devuelve "hace 2 días"
 *      rtf.format(-1, 'day'); // Devuelve "ayer" (gracias a numeric: 'auto')
 *  - En la tabla de incidencias, el texto visible será el relativo («hace 2 días»),
 *    y en el atributo `title` de la etiqueta podéis poner la fecha completa:
 *    <span title="2 de noviembre de 2026, 18:30">hace 2 días</span>
 *    ¡Esto lo pide expresamente el apartado 3.3 del proyecto!
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Intl.RelativeTimeFormat: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Intl/RelativeTimeFormat
 *  - Intl.DateTimeFormat: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Intl/DateTimeFormat
 *  - Operaciones con fechas en JavaScript: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Date
 */

