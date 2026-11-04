/**
 * ============================================================================
 *  AulaFix · public/js/main.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Hito 1 (lectura JSON, tabla básica) & Entrega final (panel completo)
 *  RA:     0612-RA1 (módulos ES, consola)
 *          0612-RA2 (sintaxis estricta, const/let, ===, JSDoc)
 *          0612-RA3 (JSON.parse protegido, setInterval para refrescar fechas)
 *          0612-RA4 (orden sin mutar array original, filter)
 *          0612-RA6 (delegación de eventos con closest, sustituir tabla PHP)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El controlador principal del lado cliente para la página `incidencias.php`.
 *  Importa los demás módulos (incidencia, vistas, fechas, preferencias), lee
 *  el JSON inyectado por el servidor, inicializa el panel interactivo,
 *  gestiona el estado global de la vista y escucha los eventos de usuario.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero explicando su función.
 *  [ ] Uso exclusivo de sintaxis de MÓDULO (import ... from ...).
 *  [ ] LECTURA SEGURA DEL JSON (apartado 3.4):
 *        · Leer el contenido de `<script id="datos-incidencias" type="application/json">`.
 *        · Parsear con `JSON.parse()` dentro de un bloque `try...catch`
 *          (Sobresaliente RA3). Manejar el caso de datos vacíos o erróneos.
 *  [ ] Convertir los objetos crudos en instancias de la clase `Incidencia`.
 *  [ ] MEJORA PROGRESIVA:
 *        · Ocultar la tabla estática de PHP y mostrar el panel interactivo.
 *  [ ] RECUPERAR PREFERENCIAS:
 *        · Cargar estado guardado de `localStorage` al iniciar (pestaña, filtros, orden).
 *  [ ] GESTIÓN DE EVENTOS MEDIANTE DELEGACIÓN (Sobresaliente 0612-RA6):
 *        · UN ÚNICO manejador de eventos en el contenedor de pestañas
 *          (usando `e.target.closest('button[role="tab"]')`).
 *        · UN ÚNICO manejador de eventos en el `thead` de la tabla para ordenar
 *          (usando `e.target.closest('th[data-columna]')`). Al hacer clic alterna
 *          entre orden ascendente y descendente.
 *        · Evento `input` en el buscador: filtra en tiempo real (equipo, descripción, autor).
 *        · Evento `change` en el desplegable de prioridad.
 *  [ ] FILTRADO Y ORDENACIÓN INMUTABLE (0612-RA4):
 *        · PROHIBIDO modificar el array original de incidencias. Usar `.filter()`
 *          y ordenar sobre una copia (ej. `[...lista].sort(...)` o `.toSorted()`).
 *  [ ] REFRESCAR FECHAS:
 *        · Usar `setInterval` para actualizar las fechas relativas cada minuto
 *          (Sobresaliente RA3).
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Estructura del estado de la aplicación en memoria:
 *      let estado = {
 *        incidenciasOriginales: [],
 *        pestanaActiva: 'todas',
 *        busquedaTexto: '',
 *        prioridadSeleccionada: '',
 *        columnaOrden: 'creada_en',
 *        ordenAscendente: false
 *      };
 *  - Flujo centralizado (función `actualizar()`):
 *      1. Filtrar lista por pestaña.
 *      2. Filtrar por texto (toLowerCase en equipo, descripción, autor).
 *      3. Filtrar por prioridad.
 *      4. Ordenar copia del array.
 *      5. Llamar a `renderizarTabla()` y actualizar contadores/resumen.
 *      6. Guardar estado en `localStorage` con `preferencias.js`.
 *  - Delegación de eventos con `closest()` (Ver apuntes Unidad 5):
 *      contenedorPestanas.addEventListener('click', (e) => {
 *        const boton = e.target.closest('[role="tab"]');
 *        if (!boton) return;
 *        const estado = boton.dataset.estado;
 *        // cambiar pestaña y actualizar
 *      });
 *  - PREGUNTA DE DEFENSA: ¿Por qué no debemos mutar el array original con sort()?
 *    (Porque si ordenas o filtras sobre el mismo array, pierdes la referencia de
 *    los datos originales o introduces efectos secundarios inesperados).
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Event delegation y Element.closest(): https://developer.mozilla.org/es/docs/Web/API/Element/closest
 *  - Inmutabilidad de arrays: toSorted(), spread [...arr].sort()
 *  - Array.prototype.filter(): https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Global_Objects/Array/filter
 *  - setInterval y clearInterval: https://developer.mozilla.org/es/docs/Web/API/setInterval
 */

