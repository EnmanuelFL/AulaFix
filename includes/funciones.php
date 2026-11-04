<?php
/*
 * ============================================================================
 *  AulaFix · includes/funciones.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Hito 1 (e(), CSRF, PRG, validación) + Entrega final (sesión y permisos)
 *  RA:     0613-RA2 (e() en toda la salida, JSON seguro) · 0613-RA3 (funciones
 *          reutilizables de validación, CSRF, PRG) · 0613-RA4 (sesión, permisos)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  La "caja de herramientas" de PHP: funciones pequeñas y reutilizables que
 *  usan TODAS las páginas. Si algo se repite en dos páginas, debería vivir aquí.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado) · organizad el fichero por bloques
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Cada función con PHPDoc: qué hace, @param y @return.
 *
 *  BLOQUE 1 · SALIDA SEGURA (Hito 1)
 *  [ ] e(mixed $valor): string → escapa con htmlspecialchars(). El código está
 *      en el apartado 5 del enunciado: copiadlo, pero ENTENDED cada argumento.
 *  [ ] Función que convierte datos a JSON seguro para meterlo en la página
 *      (incidencias.php), con json_encode y las opciones JSON_HEX_*.
 *
 *  BLOQUE 2 · SESIÓN (Entrega final)
 *  [ ] Función que arranca la sesión con la cookie segura: HttpOnly y SameSite
 *      (Sobresaliente RA4). Secure si hay HTTPS.
 *
 *  BLOQUE 3 · CSRF Y PRG (Hito 1)
 *  [ ] Generar el token CSRF (una vez por sesión) y devolverlo.
 *  [ ] Comprobar el token recibido por POST. Si no coincide → cortar la petición.
 *  [ ] redirigir(string $url): never → cabecera Location con código 303 + exit.
 *  [ ] Mensajes "flash" (se guardan en sesión, se muestran UNA vez y se borran).
 *
 *  BLOQUE 4 · VALIDACIÓN (Hito 1)
 *  [ ] Leer un campo de $_POST como string recortado (sin avisos si no existe).
 *  [ ] Validar longitud (cuidado con tildes y ñ → mb_strlen, no strlen).
 *  [ ] Validar que un valor está en una lista blanca (categoria, prioridad, estado).
 *  [ ] Validar correo.
 *
 *  BLOQUE 5 · AUTENTICACIÓN Y PERMISOS (Entrega final)
 *  [ ] usuarioActual(): ?array → el usuario de la sesión o null.
 *  [ ] exigirSesion() → si no hay sesión, redirige a login.php.
 *  [ ] exigirProfesor() → si no es profesor, responde 403 y corta.
 *  [ ] puedeBorrar(array $incidencia, array $usuario): bool
 *        profesor → todas · alumno → solo las suyas Y si siguen 'abierta'.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Hay un ejemplo completo de CSRF + PRG en los apuntes
 *    ("Procesamiento seguro de formularios"): random_bytes() + bin2hex() para
 *    el token y hash_equals() para compararlo. ¿Por qué hash_equals y no ===?
 *  - session_start() y header() fallan si ya se ha enviado HTML. Por eso cada
 *    página hace primero TODA la lógica y al final incluye cabecera.php.
 *  - session_set_cookie_params() se llama ANTES de session_start().
 *  - redirigir(): el tipo de retorno "never" (PHP 8.1) indica que la función
 *    nunca vuelve. ¿Qué pasa si se os olvida el exit después de header()?
 *  - Para leer campos sin avisos: el operador ?? y el cast (string).
 *  - Lista blanca: in_array($valor, $lista, true). ¿Qué hace el tercer
 *    parámetro true y por qué es importante?
 *  - Contraseña "con letras y números": preg_match() con dos expresiones
 *    sencillas suele ser más legible que una sola expresión gigante.
 *  - JSON seguro: investigad JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
 *    JSON_HEX_QUOT y JSON_THROW_ON_ERROR. PREGUNTA DE DEFENSA (Sobresaliente
 *    RA2): ¿qué pasaría si una descripción contiene el texto "</script>"?
 *  - PREGUNTA DE DEFENSA: ¿por qué se escapa al MOSTRAR (e()) y no al GUARDAR
 *    en la base de datos?
 *  - Los permisos se comprueban SIEMPRE en el servidor: ocultar un botón no
 *    basta (alguien puede enviar el formulario a mano). http_response_code(403).
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - htmlspecialchars: https://www.php.net/manual/es/function.htmlspecialchars.php
 *  - json_encode y sus opciones: https://www.php.net/manual/es/json.constants.php
 *  - Sesiones: session_start, session_set_cookie_params, session_regenerate_id
 *  - random_bytes, bin2hex, hash_equals
 *  - header('Location: ...', true, 303) y el patrón Post-Redirect-Get
 *  - filter_var con FILTER_VALIDATE_EMAIL: https://www.php.net/manual/es/filter.filters.validate.php
 *  - mb_strlen, trim, preg_match, in_array
 *  - Tipos de retorno never, ?array, void (PHP 8)
 *  - CSRF (OWASP): https://owasp.org/www-community/attacks/csrf
 *  - XSS (OWASP): https://owasp.org/www-community/attacks/xss/
 */

