/**
 * ============================================================================
 * AulaFix · public/js/incidencia.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Entrega final
 * RA: 0612-RA4
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Este módulo define la clase Incidencia, que representa cada incidencia
 * recibida desde el servidor mediante los datos JSON.
 *
 * La clase permite:
 * - Guardar los datos de una incidencia.
 * - Comprobar si una incidencia es urgente.
 * - Calcular los días transcurridos desde su creación.
 * - Crear objetos Incidencia a partir de datos JSON.
 *
 * ============================================================================
 */


/**
 * Representa una incidencia de AulaFix.
 *
 * Cada objeto contiene la información de una incidencia y permite
 * calcular algunas propiedades a partir de sus datos.
 *
 * @class
 */
export class Incidencia {

    // Campo privado que guarda la fecha de creación como objeto Date.
    // Al ser privado, no se puede modificar directamente desde fuera.
    #fechaCreacion;


    /**
     * Crea una nueva incidencia a partir de los datos recibidos.
     *
     * @param {Object} datos Datos de la incidencia procedentes del JSON.
     * @param {number} datos.id Identificador de la incidencia.
     * @param {string} datos.equipo Código del equipo afectado.
     * @param {string} datos.categoria Categoría de la incidencia.
     * @param {string} datos.prioridad Prioridad: baja, media o alta.
     * @param {string} datos.descripcion Descripción del problema.
     * @param {string} datos.estado Estado actual de la incidencia.
     * @param {string} datos.autor Nombre del usuario que creó la incidencia.
     * @param {string} datos.creada_en Fecha de creación de la incidencia.
     */
    constructor(datos) {

        // Guardamos los datos principales de la incidencia.
        this.id = datos.id;
        this.equipo = datos.equipo;
        this.categoria = datos.categoria;
        this.prioridad = datos.prioridad;
        this.descripcion = datos.descripcion;
        this.estado = datos.estado;
        this.autor = datos.autor;

        // Convertimos la fecha recibida en un objeto Date.
        // La guardamos en un campo privado para proteger su acceso directo.
        this.#fechaCreacion = new Date(datos.creada_en);
    }


    /**
     * Comprueba si la incidencia es urgente.
     *
     * Una incidencia se considera urgente cuando tiene prioridad alta
     * y todavía no está resuelta.
     *
     * @returns {boolean} true si es urgente y false en caso contrario.
     */
    get esUrgente() {

        return this.prioridad === "alta" && this.estado !== "resuelta";
    }


    /**
     * Calcula los días enteros transcurridos desde la creación.
     *
     * Utiliza la fecha actual y la fecha de creación para calcular
     * la diferencia en milisegundos y convertirla en días.
     *
     * @returns {number} Número de días transcurridos desde su creación.
     */
    get diasAbierta() {

        // Número de milisegundos que tiene un día.
        const msPorDia = 1000 * 60 * 60 * 24;

        // Calculamos la diferencia entre la fecha actual y la creación.
        const diferenciaMs = Date.now() - this.#fechaCreacion.getTime();

        // Devolvemos los días completos transcurridos.
        return Math.floor(diferenciaMs / msPorDia);
    }


    /**
     * Crea una incidencia a partir de un objeto recibido en formato JSON.
     *
     * Es un método estático, por lo que se puede utilizar directamente
     * desde la clase sin crear antes un objeto.
     *
     * @param {Object} datos Datos de la incidencia.
     * @returns {Incidencia} Nueva instancia de Incidencia.
     */
    static desdeJSON(datos) {

        return new Incidencia(datos);
    }

}
