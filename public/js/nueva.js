
/**
 * ============================================================================
 * AulaFix · public/js/nueva.js
 * ============================================================================
 *
 * MÓDULO: 0612 · Desarrollo web en entorno cliente
 * HITO: Entrega final (UT3, UT5)
 * RA: 0612-RA3 y 0612-RA6
 *
 * ¿QUÉ HACE ESTE FICHERO?
 *
 * Añade dos ayudas al formulario de nueva incidencia:
 * - Contador de caracteres de la descripción.
 * - Guardado y recuperación automática del borrador con localStorage.
 *
 * ============================================================================
 */

// ============================================================================
// 1. REFERENCIAS A LOS ELEMENTOS DEL FORMULARIO
// ============================================================================

// Guardamos las referencias a los elementos HTML que vamos a utilizar.

const formulario = document.querySelector("form");

const selectEquipo = document.getElementById("equipo_id");
const selectCategoria = document.getElementById("categoria");

const textareaDescripcion = document.getElementById("descripcion");

const contadorCaracteres = document.getElementById("contador-caracteres");


// ============================================================================
// 2. CONSTANTES
// ============================================================================

// Clave que utilizamos para guardar el borrador en localStorage.

const BORRADOR_KEY = "aulafix_borrador_nueva";

// Límites de caracteres establecidos por el enunciado.

const MIN_CARACTERES = 10;
const MAX_CARACTERES = 500;


// ============================================================================
// 3. ACTUALIZACIÓN DEL CONTADOR DE CARACTERES
// ============================================================================

/**
 * Actualiza el contador de caracteres de la descripción.
 *
 * Muestra los caracteres escritos y añade una clase de error
 * cuando no se cumple el mínimo o el máximo permitido.
 *
 * @returns {void}
 */
function actualizarContador() {

    // Comprobamos que existe el textarea y el contador.

    if (!textareaDescripcion || !contadorCaracteres) {
        return;
    }

    // Obtenemos el número de caracteres escritos.

    const caracteres = textareaDescripcion.value.length;

    // Mostramos el número de caracteres actuales.

    contadorCaracteres.textContent =
        `${caracteres} / ${MAX_CARACTERES} caracteres`;

    // Comprobamos si la longitud está fuera de los límites.

    const longitudIncorrecta =
        caracteres < MIN_CARACTERES || caracteres > MAX_CARACTERES;

    // Añadimos o quitamos la clase de error según corresponda.

    contadorCaracteres.classList.toggle("error", longitudIncorrecta);

    // Actualizamos el atributo de accesibilidad.

    textareaDescripcion.setAttribute(
        "aria-invalid",
        longitudIncorrecta ? "true" : "false"
    );
}


// ============================================================================
// 4. GUARDAR EL BORRADOR
// ============================================================================

/**
 * Guarda los datos actuales del formulario en localStorage.
 *
 * Se utiliza try...catch para controlar posibles errores de almacenamiento,
 * por ejemplo, si el navegador bloquea localStorage.
 *
 * @returns {void}
 */
function guardarBorrador() {

    try {

        // Obtenemos la prioridad seleccionada.

        const prioridadSeleccionada = formulario.querySelector(
            'input[name="prioridad"]:checked'
        );

        // Creamos un objeto con los datos del formulario.

        const datosFormulario = {

            equipo: selectEquipo.value,

            categoria: selectCategoria.value,

            prioridad: prioridadSeleccionada
                ? prioridadSeleccionada.value
                : "",

            descripcion: textareaDescripcion.value
        };

        // Convertimos el objeto a JSON y lo guardamos.

        localStorage.setItem(
            BORRADOR_KEY,
            JSON.stringify(datosFormulario)
        );

    } catch (error) {

        // Informamos si no se ha podido guardar el borrador.

        console.warn("No se ha podido guardar el borrador.", error);
    }
}


// ============================================================================
// 5. RECUPERAR EL BORRADOR
// ============================================================================

/**
 * Recupera los datos guardados anteriormente en localStorage.
 *
 * Si PHP ya ha rellenado algún campo después de un error de validación,
 * no sobrescribimos ese valor con el contenido del borrador.
 *
 * @returns {void}
 */
function recuperarBorrador() {

    try {

        // Recuperamos el borrador almacenado.

        const borradorGuardado = localStorage.getItem(BORRADOR_KEY);

        // Si no existe ningún borrador, terminamos.

        if (!borradorGuardado) {
            return;
        }

        // Convertimos el JSON en un objeto JavaScript.

        const borrador = JSON.parse(borradorGuardado);

        // Comprobamos que los datos recuperados son un objeto válido.

        if (!borrador || typeof borrador !== "object") {
            return;
        }

        // Recuperamos el equipo solo si PHP no ha rellenado el campo.

        if (!selectEquipo.value && typeof borrador.equipo === "string") {
            selectEquipo.value = borrador.equipo;
        }

        // Recuperamos la categoría si no existe un valor previo de PHP.

        if (!selectCategoria.value && typeof borrador.categoria === "string") {
            selectCategoria.value = borrador.categoria;
        }

        // Recuperamos la prioridad si todavía no hay ningún radio marcado.

        const prioridadActual = formulario.querySelector(
            'input[name="prioridad"]:checked'
        );

        if (!prioridadActual && typeof borrador.prioridad === "string") {

            const radioPrioridad = formulario.querySelector(
                `input[name="prioridad"][value="${borrador.prioridad}"]`
            );

            if (radioPrioridad) {
                radioPrioridad.checked = true;
            }
        }

        // Recuperamos la descripción solo si PHP no ha rellenado el textarea.

        if (
            !textareaDescripcion.value
            && typeof borrador.descripcion === "string"
        ) {

            textareaDescripcion.value = borrador.descripcion;
        }

    } catch (error) {

        // Informamos si se ha producido un error al recuperar el borrador.

        console.warn("No se ha podido recuperar el borrador.", error);
    }
}


// ============================================================================
// 6. BORRAR EL BORRADOR
// ============================================================================

/**
 * Elimina el borrador almacenado en localStorage.
 *
 * Se ejecuta cuando el usuario envía el formulario.
 *
 * @returns {void}
 */
function borrarBorrador() {

    try {

        // Eliminamos únicamente el borrador de nueva incidencia.

        localStorage.removeItem(BORRADOR_KEY);

    } catch (error) {

        // Informamos si no se ha podido eliminar.

        console.warn("No se ha podido borrar el borrador.", error);
    }
}


// ============================================================================
// 7. CONFIGURACIÓN DE LOS EVENTOS
// ============================================================================

/**
 * Configura los eventos del formulario.
 *
 * Recupera el borrador al cargar la página y guarda los cambios
 * cuando el usuario modifica los campos.
 *
 * @returns {void}
 */
function iniciarFormulario() {

    // Comprobamos que los elementos necesarios existen.

    if (
        !formulario
        || !selectEquipo
        || !selectCategoria
        || !textareaDescripcion
        || !contadorCaracteres
    ) {

        console.warn(
            "No se puede iniciar nueva.js: faltan elementos del formulario."
        );

        return;
    }

    // Primero recuperamos el borrador anterior.

    recuperarBorrador();

    // Actualizamos el contador con la descripción recuperada.

    actualizarContador();

    // Guardamos los cambios al modificar cualquier campo.

    formulario.addEventListener("input", () => {

        actualizarContador();

        guardarBorrador();
    });

    // También guardamos los cambios realizados en los desplegables.

    formulario.addEventListener("change", () => {

        guardarBorrador();
    });

    // Cuando se envía el formulario, eliminamos el borrador.

    formulario.addEventListener("submit", () => {

        borrarBorrador();
    });

    console.info("Formulario de nueva incidencia iniciado correctamente.");
}


// ============================================================================
// 8. INICIO DEL SCRIPT
// ============================================================================

// Iniciamos las funciones cuando el documento ya está preparado.

iniciarFormulario();
