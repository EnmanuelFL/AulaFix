
/**
 * ============================================================================
 * AulaFix · public/js/main.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Hito 1 y Entrega final
 * RA: 0612-RA1, RA2, RA3, RA4 y RA6
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Es el controlador principal del panel de incidencias.
 *
 * Se encarga de:
 * - Leer los datos JSON enviados por PHP.
 * - Crear objetos de la clase Incidencia.
 * - Recuperar las preferencias guardadas.
 * - Filtrar y ordenar las incidencias.
 * - Actualizar la tabla y las tarjetas del panel.
 * - Escuchar los eventos del usuario.
 * - Actualizar las fechas relativas periódicamente.
 *
 * ============================================================================
 */

// ============================================================================
// 1. IMPORTACIÓN DE LOS MÓDULOS
// ============================================================================

import { Incidencia } from "./incidencia.js";

import {
    calcularResumen,
    actualizarResumen,
    renderizarTabla,
    actualizarContadorAccesible,
    actualizarPestanas,
    actualizarOrdenTabla
} from "./vistas.js";

import {
    guardarPreferencias,
    recuperarPreferencias
} from "./preferencias.js";


// ============================================================================
// 2. REFERENCIAS A LOS ELEMENTOS DEL HTML
// ============================================================================

// Guardamos las referencias a los elementos que vamos a utilizar.
// Así evitamos tener que buscarlos continuamente en el documento.

const elementoDatos = document.getElementById("datos-incidencias");

const panelInteractivo = document.getElementById("panel-interactivo");
const listadoServidor = document.getElementById("listado-servidor");

const resumenIncidencias = document.getElementById("resumen-incidencias");

const contadorAbiertas = document.getElementById("contador-abiertas");
const contadorEnCurso = document.getElementById("contador-en-curso");
const contadorResueltas = document.getElementById("contador-resueltas");
const contadorUrgentes = document.getElementById("contador-urgentes");

const contenedorPestanas = document.getElementById("pestanas-incidencias");

const buscador = document.getElementById("buscador");
const filtroPrioridad = document.getElementById("filtro-prioridad");

const contadorResultados = document.getElementById("contador-resultados");

const cuerpoIncidencias = document.getElementById("cuerpo-incidencias");

const cabeceraTabla = document.querySelector(
    "#tabla-incidencias thead"
);


// ============================================================================
// 3. ESTADO DE LA APLICACIÓN
// ============================================================================

/**
 * Contiene los datos y las preferencias actuales del panel.
 *
 * incidenciasOriginales conserva siempre la lista completa.
 * Los filtros y la ordenación se aplican sobre copias para no modificar
 * los datos originales.
 */
let estado = {
    incidenciasOriginales: [],
    pestanaActiva: "todas",
    busquedaTexto: "",
    prioridadSeleccionada: "",
    columnaOrden: "fecha",
    ordenAscendente: false
};


// ============================================================================
// 4. LECTURA Y CONVERSIÓN DE LOS DATOS JSON
// ============================================================================

/**
 * Lee los datos JSON enviados por PHP y los convierte en objetos Incidencia.
 *
 * Se utiliza try...catch para controlar posibles errores en el JSON.
 * Si los datos están vacíos, no se inicia el panel interactivo.
 *
 * @returns {boolean} true si los datos se han leído correctamente.
 */
function cargarIncidencias() {

    try {

        // Comprobamos que existe el bloque JSON.
        if (!elementoDatos) {
            throw new Error("No se ha encontrado el bloque de datos JSON.");
        }

        // Leemos el contenido del bloque.
        const contenido = elementoDatos.textContent.trim();

        // Comprobamos que el contenido no esté vacío.
        if (contenido === "") {
            throw new Error("El bloque JSON está vacío.");
        }

        // Convertimos el texto JSON en un array de JavaScript.
        const datos = JSON.parse(contenido);

        // Comprobamos que el resultado sea un array.
        if (!Array.isArray(datos)) {
            throw new Error("Los datos recibidos no tienen el formato esperado.");
        }

        // Convertimos cada objeto recibido en una instancia de Incidencia.
        estado.incidenciasOriginales = datos.map(
            dato => Incidencia.desdeJSON(dato)
        );

        return true;

    } catch (error) {

        // Mostramos el error en la consola para facilitar su depuración.
        console.error("Error al cargar las incidencias:", error);

        return false;
    }
}


// ============================================================================
// 5. RECUPERACIÓN DE LAS PREFERENCIAS
// ============================================================================

/**
 * Recupera las preferencias guardadas anteriormente en localStorage.
 *
 * Restaura la pestaña, el texto de búsqueda, la prioridad seleccionada
 * y el orden de la tabla.
 *
 * @returns {void}
 */
function cargarPreferencias() {

    const preferencias = recuperarPreferencias();

    // Recuperamos la pestaña seleccionada.
    estado.pestanaActiva = preferencias.pestana;

    // Recuperamos el texto de búsqueda.
    estado.busquedaTexto = preferencias.filtroTexto;

    // Recuperamos el filtro de prioridad.
    estado.prioridadSeleccionada = preferencias.filtroPrioridad;

    // Recuperamos la columna de ordenación.
    estado.columnaOrden = preferencias.ordenColumna;

    // Recuperamos la dirección de ordenación.
    estado.ordenAscendente = preferencias.ordenAsc;

    // Reflejamos las preferencias en los controles del HTML.
    if (buscador) {
        buscador.value = estado.busquedaTexto;
    }

    if (filtroPrioridad) {
        filtroPrioridad.value = estado.prioridadSeleccionada;
    }
}


/**
 * Guarda las preferencias actuales del panel en localStorage.
 *
 * @returns {void}
 */
function guardarEstado() {

    guardarPreferencias({
        pestana: estado.pestanaActiva,
        filtroTexto: estado.busquedaTexto,
        filtroPrioridad: estado.prioridadSeleccionada,
        ordenColumna: estado.columnaOrden,
        ordenAsc: estado.ordenAscendente
    });
}


// ============================================================================
// 6. FILTRADO DE INCIDENCIAS
// ============================================================================

/**
 * Filtra las incidencias según la pestaña, el texto y la prioridad.
 *
 * Se utiliza filter() para crear un nuevo array sin modificar
 * la lista original.
 *
 * @returns {Array} Lista de incidencias que cumplen los filtros.
 */
function filtrarIncidencias() {

    // Empezamos siempre desde la lista original.
    let resultado = estado.incidenciasOriginales;

    // ------------------------------------------------------------
    // FILTRO POR PESTAÑA
    // ------------------------------------------------------------

    if (estado.pestanaActiva !== "todas") {

        resultado = resultado.filter(
            incidencia => incidencia.estado === estado.pestanaActiva
        );
    }

    // ------------------------------------------------------------
    // FILTRO POR TEXTO
    // ------------------------------------------------------------

    const texto = estado.busquedaTexto.toLowerCase().trim();

    if (texto !== "") {

        resultado = resultado.filter(incidencia => {

            // Comprobamos equipo, descripción y autor.
            const equipo = String(incidencia.equipo).toLowerCase();
            const descripcion = String(incidencia.descripcion).toLowerCase();
            const autor = String(incidencia.autor).toLowerCase();

            return equipo.includes(texto)
                || descripcion.includes(texto)
                || autor.includes(texto);
        });
    }

    // ------------------------------------------------------------
    // FILTRO POR PRIORIDAD
    // ------------------------------------------------------------

    if (estado.prioridadSeleccionada !== "") {

        resultado = resultado.filter(
            incidencia =>
                incidencia.prioridad === estado.prioridadSeleccionada
        );
    }

    return resultado;
}


// ============================================================================
// 7. ORDENACIÓN DE INCIDENCIAS
// ============================================================================

/**
 * Ordena las incidencias según la columna seleccionada.
 *
 * Se crea una copia del array antes de utilizar sort().
 * De esta manera no modificamos la lista original.
 *
 * @param {Array} lista Lista de incidencias que queremos ordenar.
 * @returns {Array} Nueva lista ordenada.
 */
function ordenarIncidencias(lista) {

    // Creamos una copia para evitar modificar el array original.
    const listaOrdenada = [...lista];

    // Determinamos el valor de comparación según la columna.
    listaOrdenada.sort((a, b) => {

        let valorA;
        let valorB;

        switch (estado.columnaOrden) {

            case "equipo":

                valorA = a.equipo.toLowerCase();
                valorB = b.equipo.toLowerCase();

                return estado.ordenAscendente
                    ? valorA.localeCompare(valorB)
                    : valorB.localeCompare(valorA);


            case "prioridad":

                // Establecemos un orden para las prioridades.
                const prioridades = {
                    baja: 1,
                    media: 2,
                    alta: 3
                };

                valorA = prioridades[a.prioridad] ?? 0;
                valorB = prioridades[b.prioridad] ?? 0;

                return estado.ordenAscendente
                    ? valorA - valorB
                    : valorB - valorA;


            case "fecha":

                valorA = new Date(a.creada_en).getTime();
                valorB = new Date(b.creada_en).getTime();

                return estado.ordenAscendente
                    ? valorA - valorB
                    : valorB - valorA;


            default:
                return 0;
        }
    });

    return listaOrdenada;
}


// ============================================================================
// 8. ACTUALIZACIÓN CENTRALIZADA DEL PANEL
// ============================================================================

/**
 * Aplica los filtros, ordena las incidencias y actualiza el panel.
 *
 * Esta función centraliza las operaciones necesarias cada vez que
 * el usuario cambia un filtro, una pestaña o el orden de la tabla.
 *
 * @returns {void}
 */
function actualizar() {

    // Primero filtramos las incidencias.
    const incidenciasFiltradas = filtrarIncidencias();

    // Después ordenamos el resultado sin modificar el array original.
    const incidenciasOrdenadas = ordenarIncidencias(incidenciasFiltradas);

    // Calculamos el resumen de todas las incidencias originales.
    const resumen = calcularResumen(estado.incidenciasOriginales);

    // Actualizamos las cuatro tarjetas.
    actualizarResumen(resumen, {
        abierta: contadorAbiertas,
        en_curso: contadorEnCurso,
        resuelta: contadorResueltas,
        urgentes: contadorUrgentes
    });

    // Actualizamos la tabla con las incidencias filtradas y ordenadas.
    renderizarTabla(incidenciasOrdenadas, cuerpoIncidencias);

    // Actualizamos el contador accesible.
    actualizarContadorAccesible(
        incidenciasOrdenadas.length,
        estado.incidenciasOriginales.length,
        contadorResultados
    );

    // Actualizamos el aspecto accesible de las pestañas.
    const pestanas = contenedorPestanas.querySelectorAll(
        '[role="tab"]'
    );

    actualizarPestanas(pestanas, estado.pestanaActiva);

    // Actualizamos el atributo aria-sort de las cabeceras.
    const cabeceras = cabeceraTabla.querySelectorAll(
        "th[data-columna]"
    );

    actualizarOrdenTabla(
        cabeceras,
        estado.columnaOrden,
        estado.ordenAscendente ? "asc" : "desc"
    );

    // Guardamos las preferencias para la próxima visita.
    guardarEstado();
}


// ============================================================================
// 9. EVENTOS DE LAS PESTAÑAS
// ============================================================================

/**
 * Configura la delegación de eventos para las pestañas.
 *
 * Solo se utiliza un manejador en el contenedor de pestañas.
 * closest() permite detectar el botón aunque se pulse sobre su texto.
 *
 * @returns {void}
 */
function configurarPestanas() {

    contenedorPestanas.addEventListener("click", evento => {

        const boton = evento.target.closest('button[role="tab"]');

        // Si el clic no procede de una pestaña, no hacemos nada.
        if (!boton) {
            return;
        }

        // Recuperamos el estado asociado al botón.
        estado.pestanaActiva = boton.dataset.pestana;

        // Actualizamos el panel.
        actualizar();
    });
}


// ============================================================================
// 10. EVENTOS DE ORDENACIÓN DE LA TABLA
// ============================================================================

/**
 * Configura la delegación de eventos en la cabecera de la tabla.
 *
 * Al pulsar una columna, cambia el orden entre ascendente y descendente.
 *
 * @returns {void}
 */
function configurarOrdenTabla() {

    cabeceraTabla.addEventListener("click", evento => {

        const cabecera = evento.target.closest("th[data-columna]");

        // Si el clic no procede de una columna ordenable, salimos.
        if (!cabecera) {
            return;
        }

        const columnaSeleccionada = cabecera.dataset.columna;

        // Si volvemos a pulsar la misma columna, invertimos el orden.
        if (estado.columnaOrden === columnaSeleccionada) {

            estado.ordenAscendente = !estado.ordenAscendente;

        } else {

            // Al cambiar de columna empezamos por orden ascendente.
            estado.columnaOrden = columnaSeleccionada;
            estado.ordenAscendente = true;
        }

        // Repintamos la tabla.
        actualizar();
    });
}


// ============================================================================
// 11. EVENTOS DE BÚSQUEDA Y PRIORIDAD
// ============================================================================

/**
 * Configura los eventos de los filtros de búsqueda.
 *
 * El buscador se actualiza mientras se escribe.
 * El desplegable se actualiza cuando cambia la prioridad.
 *
 * @returns {void}
 */
function configurarFiltros() {

    // Búsqueda en tiempo real.
    buscador.addEventListener("input", () => {

        estado.busquedaTexto = buscador.value;

        actualizar();
    });

    // Filtro por prioridad.
    filtroPrioridad.addEventListener("change", () => {

        estado.prioridadSeleccionada = filtroPrioridad.value;

        actualizar();
    });
}


// ============================================================================
// 12. ACTUALIZACIÓN PERIÓDICA DE LAS FECHAS
// ============================================================================

/**
 * Configura la actualización periódica del panel.
 *
 * Cada minuto se vuelve a pintar la tabla para actualizar las fechas
 * relativas, por ejemplo, de "hace 2 días" a "hace 3 días".
 *
 * @returns {number} Identificador del intervalo.
 */
function configurarActualizacionFechas() {

    return setInterval(() => {

        actualizar();

    }, 60000);
}


// ============================================================================
// 13. INICIALIZACIÓN DEL PANEL
// ============================================================================

/**
 * Inicializa el panel interactivo cuando se ha leído correctamente el JSON.
 *
 * Recupera las preferencias, configura los eventos y muestra el panel.
 * Si se produce un error antes de este punto, se mantiene la tabla PHP.
 *
 * @returns {void}
 */
function iniciarPanel() {

    // Recuperamos las preferencias anteriores.
    cargarPreferencias();

    // Configuramos los eventos de usuario.
    configurarPestanas();
    configurarOrdenTabla();
    configurarFiltros();

    // Pintamos el panel con los datos iniciales.
    actualizar();

    // Mostramos el panel interactivo.
    resumenIncidencias.hidden = false;
    panelInteractivo.hidden = false;

    // Ocultamos la tabla PHP porque el panel ya está funcionando.
    listadoServidor.hidden = true;

    // Actualizamos las fechas relativas cada minuto.
    configurarActualizacionFechas();

    console.info("Panel de AulaFix iniciado correctamente.");
}


// ============================================================================
// 14. ARRANQUE DE LA APLICACIÓN
// ============================================================================

// Comprobamos que los elementos principales del HTML existen.
const elementosCorrectos =
    elementoDatos
    && panelInteractivo
    && listadoServidor
    && resumenIncidencias
    && contenedorPestanas
    && buscador
    && filtroPrioridad
    && contadorResultados
    && cuerpoIncidencias
    && cabeceraTabla;

// Solo iniciamos el panel si tenemos todos los elementos necesarios.
if (elementosCorrectos) {

    // Primero leemos los datos JSON.
    const datosCorrectos = cargarIncidencias();

    if (datosCorrectos) {

        // Si el JSON es correcto, iniciamos el panel.
        iniciarPanel();

    } else {

        // Si hay un error, mantenemos visible la tabla de PHP.
        console.warn(
            "No se ha podido iniciar el panel. Se mantiene el listado PHP."
        );
    }

} else {

    // Indicamos qué ha ocurrido si falta algún elemento del HTML.
    console.error(
        "No se puede iniciar AulaFix: faltan elementos necesarios en el HTML."
    );
}
