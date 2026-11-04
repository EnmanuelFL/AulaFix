<?php
/*
 * ============================================================================
 *  AulaFix · public/logout.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final
 *  RA:     0613-RA4 (cierre de sesión completo)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  Cierra la sesión del usuario. NO pinta HTML: recibe el formulario POST del
 *  botón «Cerrar sesión» (en cabecera.php), destruye la sesión y redirige.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Solo acepta POST (con token CSRF). Si llega por GET, no cierra nada.
 *  [ ] Cierre de sesión COMPLETO:
 *        1. Vaciar los datos de la sesión.
 *        2. Borrar la cookie de sesión del navegador.
 *        3. Destruir la sesión en el servidor.
 *  [ ] Redirigir a login.php (o a index.php).
 *  [ ] NO borrar la cookie «Recordar mi correo»: precisamente sirve para
 *      la próxima vez.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - PREGUNTA DE DEFENSA: ¿por qué el logout va por POST con CSRF y no es un
 *    simple enlace? (Pensad en una imagen de otra web con
 *    src="http://localhost:8000/logout.php"...)
 *  - Si llega por GET, podéis responder 405 (Method Not Allowed) o redirigir.
 *  - session_destroy() por sí solo NO borra la cookie del navegador ni vacía
 *    $_SESSION en la petición actual. Por eso son 3 pasos.
 *  - Para borrar la cookie de sesión necesitáis su nombre (session_name()) y
 *    sus parámetros (session_get_cookie_params()). Hay un ejemplo en el
 *    manual de session_destroy().
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - session_destroy (mirad el ejemplo del manual): https://www.php.net/manual/es/function.session-destroy.php
 *  - session_name, session_get_cookie_params
 *  - $_SERVER['REQUEST_METHOD'] y http_response_code(405)
 */

