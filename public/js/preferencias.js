/**
 * ============================================================================
 * AulaFix · public/js/preferencias.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Hito 1 y Entrega final
 * RA: 0612-RA3
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Este módulo se encarga de guardar y recuperar las preferencias del usuario
 * para el panel interactivo de AulaFix.
 *
 * Las preferencias que se guardan son:
 *
 * - La pestaña seleccionada.
 * - El texto del buscador.
 * - El filtro de prioridad.
 * - La columna utilizada para ordenar la tabla.
 * - El sentido de la ordenación (ascendente o descendente).
 *
 * Los datos se guardan en localStorage para que se mantengan al cerrar
 * o actualizar la página.
 *
 * Todas las operaciones de almacenamiento están protegidas con try...catch
 * para evitar errores si el navegador bloquea localStorage.
 *
 * No se guardan contraseñas ni información personal.
 *
 * ============================================================================
 */

// Clave que utilizaremos para guardar las preferencias en localStorage.
const STORAGE_KEY = "aulafix_panel_prefs";

// Valores que se utilizarán cuando todavía no haya preferencias guardadas.
const PREFERENCIAS_POR_DEFECTO = {
    pestana: "todas",
    filtroTexto: "",
    filtroPrioridad: "",
    ordenColumna: "fecha",
    ordenAsc: false
};


/**
 * Guarda las preferencias del panel en localStorage.
 *
 * Antes de guardar, convertimos el objeto a formato JSON mediante
 * JSON.stringify().
 *
 * Si el almacenamiento está bloqueado o se produce algún error,
 * lo capturamos para evitar que falle el resto de la aplicación.
 *
 * @param {Object} preferencias Objeto con las preferencias del usuario.
 * @returns {boolean} true si se han guardado correctamente y false si falla.
 */
export function guardarPreferencias(preferencias) {

    try {

        // Comprobamos que se ha recibido un objeto válido.
        if (
            preferencias === null ||
            typeof preferencias !== "object" ||
            Array.isArray(preferencias)
        ) {
            return false;
        }

        // Combinamos los valores por defecto con las preferencias recibidas.
        // Así evitamos guardar campos incompletos.
        const preferenciasCompletas = {
            ...PREFERENCIAS_POR_DEFECTO,
            ...preferencias
        };

        // Convertimos el objeto JavaScript en una cadena JSON.
        const datosJSON = JSON.stringify(preferenciasCompletas);

        // Guardamos la información en el almacenamiento del navegador.
        localStorage.setItem(STORAGE_KEY, datosJSON);

        return true;

    } catch (error) {

        // Puede fallar si el navegador bloquea el almacenamiento
        // o si se ha alcanzado el espacio disponible.
        console.error("No se han podido guardar las preferencias:", error);

        return false;
    }
}


/**
 * Recupera las preferencias guardadas en localStorage.
 *
 * Utiliza JSON.parse() para convertir la cadena almacenada en un objeto.
 * Después comprueba que los datos tengan los valores esperados.
 *
 * Si no existen preferencias o se produce algún error, devuelve
 * los valores por defecto.
 *
 * @returns {Object} Objeto con las preferencias recuperadas.
 */
export function recuperarPreferencias() {

    try {

        // Intentamos obtener las preferencias guardadas.
        const datosGuardados = localStorage.getItem(STORAGE_KEY);

        // Si no existe información guardada, devolvemos los valores iniciales.
        if (datosGuardados === null) {
            return { ...PREFERENCIAS_POR_DEFECTO };
        }

        // Convertimos la cadena JSON en un objeto JavaScript.
        const preferenciasGuardadas = JSON.parse(datosGuardados);

        // Comprobamos que el contenido recuperado sea un objeto válido.
        if (
            preferenciasGuardadas === null ||
            typeof preferenciasGuardadas !== "object" ||
            Array.isArray(preferenciasGuardadas)
        ) {
            return { ...PREFERENCIAS_POR_DEFECTO };
        }

        // Combinamos las preferencias recuperadas con los valores por defecto.
        // Así, si falta algún campo, se mantiene su valor inicial.
        const preferencias = {
            ...PREFERENCIAS_POR_DEFECTO,
            ...preferenciasGuardadas
        };

        // Comprobamos que la pestaña tenga uno de los valores permitidos.
        const pestanasPermitidas = [
            "todas",
            "abierta",
            "en_curso",
            "resuelta"
        ];

        if (!pestanasPermitidas.includes(preferencias.pestana)) {
            preferencias.pestana = PREFERENCIAS_POR_DEFECTO.pestana;
        }

        // Comprobamos que el filtro de prioridad sea correcto.
        const prioridadesPermitidas = ["", "baja", "media", "alta"];

        if (!prioridadesPermitidas.includes(preferencias.filtroPrioridad)) {
            preferencias.filtroPrioridad =
                PREFERENCIAS_POR_DEFECTO.filtroPrioridad;
        }

        // Comprobamos que la columna de ordenación sea válida.
        const columnasPermitidas = ["fecha", "equipo", "prioridad"];

        if (!columnasPermitidas.includes(preferencias.ordenColumna)) {
            preferencias.ordenColumna =
                PREFERENCIAS_POR_DEFECTO.ordenColumna;
        }

        // Comprobamos que el texto del buscador sea una cadena.
        if (typeof preferencias.filtroTexto !== "string") {
            preferencias.filtroTexto =
                PREFERENCIAS_POR_DEFECTO.filtroTexto;
        }

        // Comprobamos que el sentido de ordenación sea booleano.
        if (typeof preferencias.ordenAsc !== "boolean") {
            preferencias.ordenAsc =
                PREFERENCIAS_POR_DEFECTO.ordenAsc;
        }

        return preferencias;

    } catch (error) {

        // Si el JSON está corrupto o falla localStorage,
        // devolvemos las preferencias iniciales.
        console.error("No se han podido recuperar las preferencias:", error);

        return { ...PREFERENCIAS_POR_DEFECTO };
    }
}


/**
 * Elimina las preferencias guardadas del panel.
 *
 * Esta función puede utilizarse cuando queramos restablecer
 * la configuración del usuario.
 *
 * @returns {boolean} true si se han eliminado correctamente y false si falla.
 */
export function borrarPreferencias() {

    try {

        // Eliminamos únicamente la clave utilizada por AulaFix.
        localStorage.removeItem(STORAGE_KEY);

        return true;

    } catch (error) {

        // Capturamos cualquier error relacionado con el almacenamiento.
        console.error("No se han podido borrar las preferencias:", error);

        return false;
    }
}
