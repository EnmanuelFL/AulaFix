<?php
/*
 * ============================================================================
 *  AulaFix · public/registro.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final
 *  RA:     0613-RA3 (validación en el servidor, errores por campo, PRG, CSRF)
 *          0613-RA4 (password_hash con Argon2id)
 *          0613-RA6 (INSERT con sentencia preparada)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  La página para crear una cuenta nueva. Muestra el formulario (GET) y lo
 *  procesa (POST). Solo accesible SIN sesión.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado, apartado 3.2)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Campos: nombre, correo, contraseña y repetición de la contraseña.
 *  [ ] Validación EN EL SERVIDOR:
 *        · nombre: de 2 a 80 caracteres.
 *        · correo: formato válido Y que no esté ya registrado.
 *        · contraseña: al menos 10 caracteres, con letras Y números.
 *        · repetición: igual que la contraseña.
 *  [ ] Errores mostrados JUNTO A CADA CAMPO y se conserva lo escrito.
 *  [ ] Contraseña guardada con password_hash() usando Argon2id.
 *  [ ] Formulario POST + token CSRF + PRG (redirección al terminar bien).
 *  [ ] Toda la salida escapada con e().
 *  [ ] Si ya hay sesión iniciada, no tiene sentido registrarse → redirigir.
 *
 * ----------------------------------------------------------------------------
 *  ESTRUCTURA SUGERIDA DE LA PÁGINA
 * ----------------------------------------------------------------------------
 *   1. Includes (config, funciones, db) y arrancar sesión.
 *   2. Variables iniciales: $errores = [] y $datos con los campos vacíos.
 *   3. Si es POST: comprobar CSRF → leer campos → validar → si no hay
 *      errores: guardar + mensaje flash + redirigir (PRG).
 *   4. Definir $titulo, incluir cabecera.php, pintar el formulario, incluir pie.php.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - ¿Qué ROL tiene una cuenta nueva? Pensadlo bien: si el rol viniera del
 *    formulario, cualquiera podría registrarse como profesor enviándolo a
 *    mano. ¿Cómo se crean entonces los profesores? (pista: instalar.php)
 *  - ¿Se debe volver a rellenar el campo contraseña cuando hay errores?
 *    (Nunca se devuelve una contraseña al navegador.)
 *  - "Correo no repetido": comprobadlo con una consulta, pero recordad que
 *    la restricción UNIQUE de MySQL es la última barrera. ¿Qué excepción
 *    lanza PDO si se viola? (Investigad el código de error 23000.)
 *  - ¿Dónde redirigís al terminar? Lo habitual es a login.php con un mensaje
 *    tipo «Cuenta creada, ya puedes iniciar sesión».
 *  - Atributos HTML como required, minlength, maxlength o type="email"
 *    ayudan al usuario, PERO no sustituyen a la validación del servidor
 *    (se pueden quitar con el inspector). Probadlo.
 *  - Usad <label for="..."> en cada campo (accesibilidad) y
 *    autocomplete="new-password" en las contraseñas.
 *  - Reutilizad las funciones de validación de includes/funciones.php:
 *    la rúbrica valora "funciones reutilizables para leer y validar campos".
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - password_hash + PASSWORD_ARGON2ID: https://www.php.net/manual/es/function.password-hash.php
 *  - filter_var + FILTER_VALIDATE_EMAIL
 *  - preg_match: https://www.php.net/manual/es/function.preg-match.php
 *  - mb_strlen (¿por qué no strlen con "Ñandú"?)
 *  - PDOException y getCode()
 *  - Patrón Post-Redirect-Get (PRG) y código 303
 *  - Atributo autocomplete en formularios (MDN)
 */

