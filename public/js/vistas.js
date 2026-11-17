/**
 * ============================================================================
 * AulaFix · public/js/vistas.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Entrega final (UT4, UT5)
 * RA: 0612-RA4 y 0612-RA6
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Este módulo se encarga de generar y actualizar los elementos visuales
 * del panel de incidencias.
 *
 * Incluye:
 * - Cálculo de los contadores mediante reduce().
 * - Actualización de las tarjetas de resumen.
 * - Pintado de la tabla de incidencias.
 * - Actualización de atributos de accesibilidad ARIA.
 *
 * IMPORTANTE:
 * Para evitar problemas de seguridad, los datos se introducen en el DOM
 * utilizando textContent y createTextNode().
 *
 * No se utiliza innerHTML para insertar datos de incidencias.
 *
 * ============================================================================
 */

// Importamos las funciones encargadas de formatear las fechas.
import {
    formatearFechaCompleta,
    formatearTiempoRelativo
} from "./fechas.js";


/**
 * Calcula los contadores del panel recorriendo la lista una sola vez.
 *
 * Se utiliza reduce() para acumular el número de incidencias de cada estado
 * y el número de incidencias urgentes.
 *
 * Una incidencia urgente es aquella que tiene prioridad alta y todavía
 * no está resuelta.
 *
 * @param {Array} listaIncidencias Lista de incidencias.
 * @returns {Object} Objeto con los contadores de cada estado y urgencias.
 */
export function calcularResumen(listaIncidencias) {

    // Valores iniciales de los contadores.
    const resumenInicial = {
        abierta: 0,
        en_curso: 0,
        resuelta: 0,
        urgentes: 0
    };

    // Si no recibimos un array, devolvemos los contadores a cero.
    if (!Array.isArray(listaIncidencias)) {
        return resumenInicial;
    }

    // Recorremos la lista una sola vez utilizando reduce().
    return listaIncidencias.reduce((resumen, incidencia) => {

        // Sumamos uno al contador correspondiente al estado.
        if (incidencia.estado === "abierta") {
            resumen.abierta++;
        } else if (incidencia.estado === "en_curso") {
            resumen.en_curso++;
        } else if (incidencia.estado === "resuelta") {
            resumen.resuelta++;
        }

        // Comprobamos si la incidencia es urgente.
        const esUrgente = incidencia.esUrgente !== undefined
            ? incidencia.esUrgente
            : incidencia.prioridad === "alta"
                && incidencia.estado !== "resuelta";

        if (esUrgente) {
            resumen.urgentes++;
        }

        // Devolvemos el acumulador para la siguiente vuelta.
        return resumen;

    }, resumenInicial);
}


/**
 * Actualiza el contenido de las cuatro tarjetas del resumen.
 *
 * Recibe los elementos HTML de las tarjetas mediante un objeto.
 * De esta forma no dependemos de identificadores HTML concretos.
 *
 * Ejemplo de elementos:
 * {
 *     abierta: elementoAbierta,
 *     en_curso: elementoEnCurso,
 *     resuelta: elementoResuelta,
 *     urgentes: elementoUrgentes
 * }
 *
 * @param {Object} resumen Contadores calculados por calcularResumen().
 * @param {Object} elementos Elementos HTML donde mostrar los contadores.
 * @returns {void}
 */
export function actualizarResumen(resumen, elementos) {

    // Comprobamos que tenemos los datos y los elementos necesarios.
    if (!resumen || !elementos) {
        return;
    }

    // Actualizamos cada tarjeta utilizando textContent.
    if (elementos.abierta) {
        elementos.abierta.textContent = resumen.abierta;
    }

    if (elementos.en_curso) {
        elementos.en_curso.textContent = resumen.en_curso;
    }

    if (elementos.resuelta) {
        elementos.resuelta.textContent = resumen.resuelta;
    }

    if (elementos.urgentes) {
        elementos.urgentes.textContent = resumen.urgentes;
    }
}


/**
 * Comprueba si una incidencia es urgente.
 *
 * Primero intenta utilizar la propiedad calculada esUrgente de la clase
 * Incidencia. Si recibe un objeto normal, comprueba sus datos directamente.
 *
 * @param {Object} incidencia Incidencia que queremos comprobar.
 * @returns {boolean} true si es urgente y false en caso contrario.
 */
function comprobarUrgencia(incidencia) {

    if (incidencia.esUrgente !== undefined) {
        return incidencia.esUrgente;
    }

    return incidencia.prioridad === "alta"
        && incidencia.estado !== "resuelta";
}


/**
 * Crea una celda de tabla e introduce su contenido de forma segura.
 *
 * Se utiliza textContent para evitar que el navegador interprete los datos
 * recibidos como código HTML.
 *
 * @param {string|number} contenido Texto que se mostrará en la celda.
 * @returns {HTMLTableCellElement} Celda creada.
 */
function crearCelda(contenido) {

    const celda = document.createElement("td");

    // Convertimos el contenido a texto antes de introducirlo.
    celda.textContent = contenido ?? "";

    return celda;
}

/**
 * Crea una celda que contiene una etiqueta con una clase CSS.
 *
 * Se utiliza para mostrar los estados y las prioridades
 * con diferentes colores.
 *
 * @param {string} contenido Texto de la etiqueta.
 * @param {string} clase Clase CSS que se aplicará.
 * @returns {HTMLTableCellElement} Celda creada.
 */
function crearCeldaEtiquetada(contenido, clase) {

    // Creamos la celda de la tabla.
    const celda = document.createElement("td");

    // Creamos un elemento span para mostrar la etiqueta.
    const etiqueta = document.createElement("span");

    // Aplicamos las clases CSS.
    etiqueta.className = `etiqueta ${clase}`;

    // Introducimos el contenido como texto.
    etiqueta.textContent = contenido ?? "";

    // Añadimos la etiqueta dentro de la celda.
    celda.appendChild(etiqueta);

    return celda;
}


/**
 * Genera las filas de la tabla de incidencias.
 *
 * Las filas se construyen dentro de un DocumentFragment y se insertan
 * todas juntas al final. Esto evita modificar el DOM en cada fila.
 *
 * Los datos se introducen mediante textContent y createTextNode(),
 * evitando utilizar innerHTML con información de los usuarios.
 *
 * @param {Array} listaIncidencias Lista de incidencias que se mostrarán.
 * @param {HTMLTableSectionElement} tbodyElement Elemento tbody de la tabla.
 * @returns {void}
 */
export function renderizarTabla(listaIncidencias, tbodyElement) {

    // Comprobamos que el elemento recibido es válido.
    if (!tbodyElement) {
        return;
    }

    // Limpiamos las filas anteriores antes de pintar las nuevas.
    tbodyElement.replaceChildren();

    // Creamos un fragmento para acumular todas las filas.
    const fragmento = document.createDocumentFragment();

    // Si no recibimos un array, no tenemos nada que pintar.
    if (!Array.isArray(listaIncidencias)) {
        return;
    }

    // Recorremos las incidencias para crear una fila por cada una.
    listaIncidencias.forEach(incidencia => {

        // Creamos una nueva fila.
        const fila = document.createElement("tr");

        // Comprobamos si la incidencia es urgente.
        if (comprobarUrgencia(incidencia)) {
            fila.classList.add("urgente");
        }

        // ------------------------------------------------------------
        // CELDA DEL IDENTIFICADOR Y ENLACE AL DETALLE
        // ------------------------------------------------------------

        const celdaId = document.createElement("td");
        const enlace = document.createElement("a");

        // El enlace permite acceder al detalle de la incidencia.
        enlace.href = `incidencia.php?id=${encodeURIComponent(incidencia.id)}`;

        // El identificador se introduce como texto, nunca como HTML.
        enlace.textContent = incidencia.id ?? "";

        celdaId.appendChild(enlace);
        fila.appendChild(celdaId);

        // ------------------------------------------------------------
        // RESTO DE COLUMNAS
        // ------------------------------------------------------------

        // Mostramos los datos generales.
fila.appendChild(crearCelda(incidencia.equipo));
fila.appendChild(crearCelda(incidencia.categoria));

// Mostramos la prioridad mediante una etiqueta de color.
fila.appendChild(
    crearCeldaEtiquetada(
        incidencia.prioridad,
        `prioridad-${incidencia.prioridad}`
    )
);

// Mostramos la descripción.
fila.appendChild(crearCelda(incidencia.descripcion));

// Mostramos el estado mediante una etiqueta de color.
fila.appendChild(
    crearCeldaEtiquetada(
        incidencia.estado.replace("_", " "),
        `estado-${incidencia.estado.replace("_", "-")}`
    )
);

// Mostramos el autor.
fila.appendChild(crearCelda(incidencia.autor));

        // ------------------------------------------------------------
        // CELDA DE LA FECHA
        // ------------------------------------------------------------

        const celdaFecha = document.createElement("td");

        // Calculamos la fecha relativa, por ejemplo: "hace 2 días".
        const fechaRelativa = formatearTiempoRelativo(incidencia.creada_en);

        // La mostramos como texto.
        celdaFecha.textContent = fechaRelativa;

        // Añadimos la fecha completa como tooltip.
        celdaFecha.title = formatearFechaCompleta(incidencia.creada_en);

        fila.appendChild(celdaFecha);

        // Añadimos la fila al fragmento, no directamente al tbody.
        fragmento.appendChild(fila);
    });

    // Insertamos todas las filas en el DOM de una sola vez.
    tbodyElement.appendChild(fragmento);
}


/**
 * Actualiza el contador accesible de incidencias.
 *
 * El atributo aria-live="polite" permite que los lectores de pantalla
 * anuncien los cambios cuando se aplican filtros o búsquedas.
 *
 * @param {number} mostradas Número de incidencias que se están mostrando.
 * @param {number} total Número total de incidencias disponibles.
 * @param {HTMLElement} contadorElement Elemento donde mostrar el mensaje.
 * @returns {void}
 */
export function actualizarContadorAccesible(
    mostradas,
    total,
    contadorElement
) {

    if (!contadorElement) {
        return;
    }

    // Indicamos que los cambios deben anunciarse sin interrumpir.
    contadorElement.setAttribute("aria-live", "polite");

    // Mostramos cuántas incidencias se ven respecto al total.
    contadorElement.textContent =
        `Mostrando ${mostradas} de ${total} incidencias`;
}


/**
 * Actualiza la pestaña seleccionada mediante el atributo aria-selected.
 *
 * La pestaña activa recibe el valor "true" y las demás reciben "false".
 *
 * @param {NodeList|Array} pestanas Lista de elementos de las pestañas.
 * @param {string} pestanaActiva Identificador de la pestaña seleccionada.
 * @returns {void}
 */
export function actualizarPestanas(pestanas, pestanaActiva) {

    if (!pestanas) {
        return;
    }

    // Recorremos todas las pestañas.
    pestanas.forEach(pestana => {

        // Comprobamos si esta pestaña es la activa.
        const estaActiva =
            pestana.dataset.pestana === pestanaActiva;

        // Actualizamos el atributo de accesibilidad.
        pestana.setAttribute(
            "aria-selected",
            estaActiva ? "true" : "false"
        );
    });
}


/**
 * Actualiza el atributo aria-sort de las cabeceras de la tabla.
 *
 * La columna ordenada recibe "ascending" o "descending".
 * El resto de columnas reciben "none".
 *
 * Se espera que cada cabecera tenga un atributo data-columna.
 *
 * Ejemplo:
 * <th data-columna="fecha">Fecha</th>
 *
 * @param {NodeList|Array} cabeceras Lista de cabeceras de la tabla.
 * @param {string} columnaOrdenada Nombre de la columna ordenada.
 * @param {string} direccion Dirección del orden: "asc" o "desc".
 * @returns {void}
 */
export function actualizarOrdenTabla(
    cabeceras,
    columnaOrdenada,
    direccion
) {

    if (!cabeceras) {
        return;
    }

    // Traducimos la dirección al valor que utiliza ARIA.
    let ariaSort = "none";

    if (direccion === "asc") {
        ariaSort = "ascending";
    } else if (direccion === "desc") {
        ariaSort = "descending";
    }

    // Actualizamos todas las cabeceras.
    cabeceras.forEach(cabecera => {

        // Comprobamos si esta es la columna que está ordenada.
        if (cabecera.dataset.columna === columnaOrdenada) {
            cabecera.setAttribute("aria-sort", ariaSort);
        } else {
            cabecera.setAttribute("aria-sort", "none");
        }
    });
}
