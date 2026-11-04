<?php
/*
 * ============================================================================
 *  AulaFix · public/login.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final
 *  RA:     0613-RA4 (password_verify, regenerar sesión, cookie «recordar»,
 *          mismo mensaje de error, cookie de sesión segura)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  La página para iniciar sesión. Muestra el formulario (GET) y comprueba
 *  el correo y la contraseña (POST). Si todo va bien, guarda al usuario en la
 *  sesión y lo manda al listado de incidencias.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado, apartado 3.2 y rúbrica RA4)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Formulario POST con correo, contraseña, casilla «Recordar mi correo»
 *      y token CSRF.
 *  [ ] Comprobar la contraseña con password_verify().
 *  [ ] El MISMO mensaje de error si el correo no existe o si la contraseña es
 *      incorrecta (Sobresaliente). Por ejemplo: «Correo o contraseña incorrectos».
 *  [ ] Al entrar: REGENERAR el identificador de sesión.
 *  [ ] Guardar en la sesión solo lo necesario (id, nombre, rol).
 *  [ ] Casilla «Recordar mi correo»: guarda SOLO el correo en una cookie
 *      durante 30 días, con atributos seguros.
 *  [ ] Si la cookie existe, el campo correo aparece ya relleno (escapado con e()).
 *  [ ] Si la casilla NO se marca, borrar la cookie si existía.
 *  [ ] Tras un login correcto: redirigir (PRG) a incidencias.php.
 *  [ ] Comentar el PORQUÉ de cada medida de seguridad (lo exige la rúbrica).
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - session_regenerate_id(true): PREGUNTA DE DEFENSA → ¿qué ataque evita?
 *    (Investigad «fijación de sesión» / session fixation.)
 *  - ¿Por qué el mismo mensaje para los dos errores? Pensad en alguien que
 *    quiere averiguar qué correos están registrados.
 *  - ¿Por qué NO se guarda la contraseña (ni el hash) en la cookie ni en la
 *    sesión?
 *  - setcookie() con array de opciones: expires, path, httponly, samesite
 *    (y secure si usáis HTTPS). Hay un ejemplo en los apuntes de la Unidad 4
 *    (cookie 'tema_preferido').
 *  - Borrar una cookie = volver a enviarla con una fecha de caducidad pasada.
 *  - setcookie() también envía una cabecera: tiene que ir ANTES de cualquier HTML.
 *  - Una casilla (checkbox) desmarcada NO se envía en $_POST. ¿Cómo lo comprobáis?
 *  - Extra: password_needs_rehash() permite actualizar hashes antiguos.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - password_verify: https://www.php.net/manual/es/function.password-verify.php
 *  - session_regenerate_id: https://www.php.net/manual/es/function.session-regenerate-id.php
 *  - setcookie (opciones httponly, samesite, secure): https://www.php.net/manual/es/function.setcookie.php
 *  - $_COOKIE
 *  - Ataques: fijación de sesión, enumeración de usuarios
 *  - Atributos de cookies (MDN): https://developer.mozilla.org/es/docs/Web/HTTP/Cookies
 */

