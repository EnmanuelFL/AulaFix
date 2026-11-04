/**
 * ============================================================================
 *  AulaFix · public/js/nueva.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Entrega final (UT3, UT5)
 *  RA:     0612-RA3 (localStorage para borrador con try/catch)
 *          0612-RA6 (manipulación del DOM: contador de caracteres en vivo)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Script específico cargado como módulo en `nueva.php`. Aporta dos ayudas
 *  al usuario en el formulario de creación de incidencia:
 *    1. Un contador interactivo de caracteres para la descripción (10 a 500).
 *    2. Guardado automático de borrador en `localStorage` para no perder lo
 *       escrito si se cierra la pestaña por accidente.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero y comentarios con JSDoc en las funciones.
 *  [ ] CONTADOR DE CARACTERES:
 *        · Escuchar el evento `input` en el <textarea> de la descripción.
 *        · Actualizar un elemento del DOM mostrando los caracteres escritos
 *          (ejemplo: "145 / 500 caracteres").
 *        · Cambiar estilo visual (añadir clase .error o cambiar color) si tiene
 *          menos de 10 caracteres o excede los 500.
 *  [ ] BORRADOR EN LOCALSTORAGE:
 *        · Al escribir (evento `input`), guardar en `localStorage` los campos
 *          del formulario (equipo, categoría, prioridad seleccionada, descripción).
 *        · Al cargar la página: si hay un borrador guardado en `localStorage`,
 *          restaurar los valores en los campos.
 *        · Proteger las llamadas a `localStorage` con `try...catch` (RA3).
 *        · Al enviar el formulario con éxito (evento `submit`): limpiar el borrador
 *          de `localStorage` para no rellenarlo la próxima vez.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Clave recomendada para el borrador: const BORRADOR_KEY = 'aulafix_borrador_nueva';
 *  - Para guardar el borrador:
 *      const datosFormulario = {
 *        equipo: selectEquipo.value,
 *        categoria: selectCategoria.value,
 *        prioridad: form.querySelector('input[name="prioridad"]:checked')?.value || '',
 *        descripcion: textareaDescripcion.value
 *      };
 *  - Cuidado con sobreescribir datos que ya venían rellenados por PHP:
 *    si PHP ya pintó valores tras un error del servidor (PRG), respetadlos
 *    o coordinadlo bien.
 *  - El contador de caracteres es una excelente ayuda de accesibilidad y UX.
 *    Podéis asociarlo con aria-describedby al textarea.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Evento input en HTMLFormElement / HTMLTextAreaElement:
 *    https://developer.mozilla.org/es/docs/Web/API/Element/input_event
 *  - HTMLTextAreaElement.textLength / String.length
 *  - Window.localStorage y eventos de formulario (submit)
 */

