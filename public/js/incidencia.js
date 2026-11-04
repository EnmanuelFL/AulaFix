/**
 * ============================================================================
 *  AulaFix · public/js/incidencia.js
 * ============================================================================
 *  MÓDULO: 0612 · Desarrollo web en entorno cliente
 *  HITO:   Entrega final (UT4)
 *  RA:     0612-RA4 (clase Incidencia, propiedades calculadas, campo privado,
 *          organización en módulos)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Módulo que define y exporta la clase del modelo de dominio del cliente:
 *  `Incidencia`. Modela los datos que vienen del JSON del servidor y ofrece
 *  propiedades calculadas y métodos para facilitar el filtrado y ordenación.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado y la rúbrica 0612-RA4)
 * ----------------------------------------------------------------------------
 *  [ ] Cabecera de fichero explicando qué hace.
 *  [ ] Exportar la clase `Incidencia` (export default o export named).
 *  [ ] Constructor que reciba el objeto de datos según el contrato JSON:
 *      { id, equipo, categoria, prioridad, descripcion, estado, autor, creada_en }
 *  [ ] Campo privado en la clase (exigido para el Sobresaliente en RA4,
 *      por ejemplo `#fechaCreacion` o `#id`).
 *  [ ] Propiedades calculadas (getters):
 *        · `get esUrgente()`: devuelve true si prioridad === 'alta' y estado !== 'resuelta'.
 *        · `get diasAbierta()`: número de días enteros transcurridos desde su creación.
 *  [ ] Documentación completa con JSDoc en la clase y todos sus métodos.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - En el constructor podéis guardar la fecha internamente como objeto Date:
 *      this.#fecha = new Date(datos.creada_en);
 *  - Para calcular `diasAbierta`:
 *      const msPorDia = 1000 * 60 * 60 * 24;
 *      const diferenciaMs = Date.now() - this.#fecha.getTime();
 *      return Math.floor(diferenciaMs / msPorDia);
 *  - También podéis tener un método estático de factoría similar al que
 *    visteis en los ejercicios de POO:
 *      static desdeJSON(obj) { return new Incidencia(obj); }
 *  - Recordad que los campos privados en JS usan el prefijo `#` (ES2022).
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - Clases en JavaScript (ES6+): https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Classes
 *  - Campos privados (#campo): https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Classes/Private_properties
 *  - Getters en clases: https://developer.mozilla.org/es/docs/Web/JavaScript/Reference/Functions/get
 */

