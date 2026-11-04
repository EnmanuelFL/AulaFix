/**
 * ============================================================================
 *  AulaFix · public/js/preferencias.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Hito 1 & Entrega final
 *  RA:     0612-RA3 (localStorage, persistencia con JSON.stringify / parse,
 *          protegido con try/catch)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Módulo encargado de persistir y recuperar del navegador las preferencias
 *  del usuario para el panel interactivo:
 *    · Pestaña activa seleccionada (Todas, Abiertas, En curso, Resueltas).
 *    · Texto del buscador.
 *    · Filtro de prioridad seleccionado.
 *    · Columna y dirección de ordenación de la tabla.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero explicando su propósito.
 *  [ ] Exportar funciones para guardar y leer preferencias en `localStorage`.
 *  [ ] Documentación con JSDoc en cada función.
 *  [ ] Proteger TODAS las operaciones de `localStorage` con `try...catch`
 *      (exigido para el Sobresaliente en 0612-RA3 por si el usuario tiene las
 *      cookies/almacenamiento bloqueado o el JSON está corrupto).
 *  [ ] Usar `JSON.stringify()` para guardar y `JSON.parse()` para leer.
 *  [ ] Devolver valores por defecto seguros si la clave no existe o falla la lectura.
 *  [ ] NO almacenar información sensible (contraseñas, datos personales) en el navegador.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Estructura sugerida para el objeto de preferencias:
 *      {
 *        pestana: 'todas',      // 'todas' | 'abierta' | 'en_curso' | 'resuelta'
 *        filtroTexto: '',
 *        filtroPrioridad: '',   // '' | 'baja' | 'media' | 'alta'
 *        ordenColumna: 'fecha', // 'fecha' | 'equipo' | 'prioridad'
 *        ordenAsc: false        // true | false
 *      }
 *  - Constante para la clave de almacenamiento (ej. const STORAGE_KEY = 'aulafix_panel_prefs';).
 *  - Mirad el ejercicio de clase de la Unidad 3 sobre LocalStorage y JSON:
 *    siempre validar el resultado de parsear antes de darlo por bueno.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Window.localStorage: https://developer.mozilla.org/es/docs/Web/API/Window/localStorage
 *  - JSON.stringify y JSON.parse
 *  - Excepciones en localStorage: QuotaExceededError, SecurityError (modo incógnito)
 */

