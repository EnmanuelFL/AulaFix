/**
 * ============================================================================
 * AulaFix · public/js/fechas.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Hito 1
 * RA: 0612-RA3
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Este archivo contiene funciones auxiliares para trabajar con las fechas
 * de las incidencias de AulaFix.
 *
 * Permite mostrar las fechas de dos maneras:
 *
 * 1. Fecha completa en español.
 *    Ejemplo: 2 de noviembre de 2026, 18:30.
 *
 * 2. Tiempo transcurrido desde una fecha.
 *    Ejemplo: hace 2 días, hace 3 horas o ayer.
 *
 * También comprueba que las fechas sean válidas para evitar errores.
 *
 * ============================================================================
 */


/**
 * Formatea una fecha completa utilizando el formato español.
 *
 * Utiliza Intl.DateTimeFormat para mostrar el día, el mes, el año
 * y la hora de una manera fácil de leer.
 *
 * Ejemplo:
 * 2026-11-02T18:30:00+01:00
 * Resultado: 2 de noviembre de 2026, 18:30
 *
 * @param {string|Date} fecha Fecha que queremos convertir.
 * @returns {string} Fecha completa formateada o un mensaje si no es válida.
 */
export function formatearFechaCompleta(fecha) {

    // Convertimos el valor recibido en un objeto Date.
    const fechaObjeto = new Date(fecha);

    // Comprobamos si la fecha es válida.
    // getTime() devuelve NaN cuando la fecha no se puede interpretar.
    if (Number.isNaN(fechaObjeto.getTime())) {
        return "Fecha no válida";
    }

    // Opciones para mostrar la fecha en formato español.
    const opciones = {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    };

    // Creamos el formateador indicando el idioma español.
    const formateador = new Intl.DateTimeFormat("es-ES", opciones);

    // Devolvemos la fecha con el formato establecido.
    return formateador.format(fechaObjeto);
}


/**
 * Calcula cuánto tiempo ha pasado desde una fecha y lo muestra
 * en un formato relativo.
 *
 * Utiliza Date para calcular la diferencia temporal e
 * Intl.RelativeTimeFormat para mostrar el resultado en español.
 *
 * Ejemplos:
 * - hace 2 días
 * - hace 3 horas
 * - ayer
 * - dentro de 2 días
 *
 * @param {string|Date} fecha Fecha que queremos comparar.
 * @returns {string} Tiempo relativo o un mensaje si la fecha no es válida.
 */
export function formatearTiempoRelativo(fecha) {

    // Convertimos el valor recibido en un objeto Date.
    const fechaObjeto = new Date(fecha);

    // Comprobamos si la fecha es válida.
    if (Number.isNaN(fechaObjeto.getTime())) {
        return "Fecha no válida";
    }

    // Obtenemos la fecha y hora actuales.
    const ahora = new Date();

    // Calculamos la diferencia en segundos entre las dos fechas.
    // Si el resultado es negativo, la fecha está en el pasado.
    // Si es positivo, la fecha está en el futuro.
    const diferenciaSegundos = Math.round(
        (fechaObjeto.getTime() - ahora.getTime()) / 1000
    );

    // Para diferencias muy pequeñas mostramos un texto sencillo.
    if (Math.abs(diferenciaSegundos) < 5) {

        if (diferenciaSegundos < 0) {
            return "hace unos segundos";
        }

        if (diferenciaSegundos > 0) {
            return "en unos segundos";
        }

        return "ahora";
    }

    // Creamos el formateador de tiempo relativo en español.
    // numeric: "auto" permite mostrar palabras como "ayer" o "mañana".
    const formateador = new Intl.RelativeTimeFormat("es-ES", {
        numeric: "auto"
    });

    // Variables para guardar el valor y la unidad que vamos a utilizar.
    let cantidad;
    let unidad;

    // Calculamos el tiempo según la diferencia en segundos.

    // Menos de un minuto: segundos.
    if (Math.abs(diferenciaSegundos) < 60) {

        cantidad = Math.round(diferenciaSegundos);
        unidad = "second";

    // Menos de una hora: minutos.
    } else if (Math.abs(diferenciaSegundos) < 3600) {

        cantidad = Math.round(diferenciaSegundos / 60);
        unidad = "minute";

    // Menos de un día: horas.
    } else if (Math.abs(diferenciaSegundos) < 86400) {

        cantidad = Math.round(diferenciaSegundos / 3600);
        unidad = "hour";

    // Menos de un mes aproximadamente: días.
    } else if (Math.abs(diferenciaSegundos) < 2592000) {

        cantidad = Math.round(diferenciaSegundos / 86400);
        unidad = "day";

    // Menos de un año aproximadamente: meses.
    } else if (Math.abs(diferenciaSegundos) < 31536000) {

        cantidad = Math.round(diferenciaSegundos / 2592000);
        unidad = "month";

    // Un año o más: años.
    } else {

        cantidad = Math.round(diferenciaSegundos / 31536000);
        unidad = "year";
    }

    // Intl.RelativeTimeFormat genera el texto en español.
    // Por ejemplo: "hace 2 días" o "dentro de 3 horas".
    return formateador.format(cantidad, unidad);
}
