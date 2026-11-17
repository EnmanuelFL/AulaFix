<?php declare(strict_types= 1);
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
/** 
 * Escapa un texto antes de escribirlo en el HTML. 
 * Evita que un usuario meta etiquetas o scripts en la página (XSS). 
 * 
 * @param mixed $valor del texto que se va a mostrar. 
 * @return string Texto seguro para HTML. 
 */ 
function e(mixed $valor): string 
{ 
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); 
}

/**
 * Parametros que se usaran en las funciones jsonSeguro e iniciarSesion
 * @param mixed $datos
 * @return string|void para devolver falso o verdadero mediante las funciones 
 */
function jsonSeguro(mixed $datos): string{
    return json_encode($datos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
}
/**
 * Inicia la sesión con parámetros seguros
 * @return string|void para devolver falso o verdadero mediante las funciones 
 */
function iniciarSesion(): void{
    if (session_status() === PHP_SESSION_NONE){
        session_set_cookie_params([
            'lifetime' => 0, // Duración de la sesión en segundos (30 días)
            'path' => '/', // Ruta de la sesión
            'domain' => '', // Dominio de la sesión
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // Solo por HTTPS si el sitio usa HTTPS
            'httponly' => true, // Protege la cookie de JavaScript
            'samesite' => 'Lax' // Protege la cookie de ataques CSRF
        ]);
        session_start();
    }
}

/**
 * verifica el inicio de sesion
 * @return string
 */
function tokenCsrf(): string{
    iniciarSesion();
    if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * /valida el token CSRF recibido y corta la petición si no coincide
 * @param string $token
 * @return void
 */
function verificarCsrf(string $token): void{
    iniciarSesion();
    $esperado = ($_SESSION['csrf'] ?? '');
    if ($esperado === '' || !hash_equals($esperado, $token)) {
        http_response_code(403);
        exit('Token CSRF no valido.');
    }
}

/**
 * redirige con codigo HTTP 303 y detiene la ejecucion
 * @param string $url
 * @return never
 */
function redirigir(string $url): never {
    header("Location: $url", true, 303);
    exit;
}

/** Guarda un mensaje de aviso justo antes de redirigir para mostrarlo en la pagina de destino 
 * @param string $mensaje
 * @return void
 * 
 */
function ponerFlash(string $mensaje): void{
    iniciarSesion();
    $_SESSION['flash'] = $mensaje;
}

/** lee el mensaje de flash para mostralo una sola vez y lo borra al instante para que no 
 * reaparezca al recargar
 * @return ?string (el ? delante de string significa: puede devolver un string o null).
 * 
 */
function obtenerFlash(): ?string{
    iniciarSesion();
    if (!isset($_SESSION['flash'])){
        return null;
    }
    $mensaje = ($_SESSION['flash']); // Guarda el texto en una variable temporal antes de borrarlo.
    unset($_SESSION['flash']); // Borra 'flash' de $_SESSION para que no salga dos veces.
    return $mensaje;
}

/** lee un campo de $_POST como string recortado (sin avisos si no existe)
 * @param string $campo
 * @return string
 */
function leerPost(string $campo): string{
    return trim((string)($_POST[$campo] ?? ''));
}
/**
 * Cuenta los caracteres reales UTF-8 de un texto.
 * Usa mb_strlen si está disponible y, si falta la extensión mbstring, usa preg_match_all('/./u').
 *
 * @param string $texto Texto a medir.
 * @return int Número de caracteres.
 */
function longitudTexto(string $texto): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($texto, 'UTF-8');
    }
    $cuenta = preg_match_all('/./us', $texto);
    return $cuenta !== false ? $cuenta : strlen($texto);
}

/**
 * Convierte un texto a minúsculas respetando UTF-8 si mbstring está activo.
 *
 * @param string $texto Texto original.
 * @return string Texto en minúsculas.
 */
function aMinusculas(string $texto): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($texto, 'UTF-8') : strtolower($texto);
}

/** Comprueba que un texto tenga un minimo y maximo de caracteres reales
 * @param string $texto
 * @param int $min
 * @param int $max
 * @return bool
 */
function longitudValida(string $texto, int $min, int $max): bool{
    $longitud = longitudTexto($texto);
    return $longitud >= $min && $longitud <= $max;
}

/** Verifica que categoria, prioridad o estado sea uno de los valores permitidos de config.php
 *  @param string $valor
 * @param array $permitidos
 * @return bool
 */
function enListaBlanca(string $valor, array $permitidos): bool{
    return in_array($valor, $permitidos, true);
}



/** Comprueba que un correo tiene formato válido y que cabe en la columna de la BD
 * @param string $email
 * @return bool
 */
function emailValido(string $email): bool
{
    return longitudTexto($email) <= 190 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/** Devuelve el usuario que ha iniciado sesión (id, nombre, rol) o null si no hay sesión
 * @return array|null
 */
function usuarioActual(): ?array
{
    iniciarSesion();
    $usuario = $_SESSION['usuario'] ?? null;
    return is_array($usuario) ? $usuario : null;
}

/** Exige tener sesión iniciada: si no la hay, manda al login. Devuelve el usuario
 * @return array
 */
function exigirSesion(): array
{
    $usuario = usuarioActual();
    if ($usuario === null) {
        redirigir('login.php');
    }
    return $usuario;
}

/** Exige que el usuario sea profesor: si no lo es, responde 403 y corta la petición.
 * El permiso se comprueba SIEMPRE en el servidor (ocultar un botón no basta)
 * @return array
 */
function exigirProfesor(): array
{
    $usuario = exigirSesion();
    if ($usuario['rol'] !== 'profesor') {
        http_response_code(403);
        exit('Acceso denegado: esta acción es solo para el profesorado.');
    }
    return $usuario;
}

/** Indica si un usuario puede borrar una incidencia.
 * Profesor: todas. Alumno: solo las suyas y mientras sigan abiertas
 * @param array $incidencia Debe incluir usuario_id y estado
 * @param array $usuario    Usuario de la sesión (id, rol)
 * @return bool
 */
function puedeBorrar(array $incidencia, array $usuario): bool
{
    if ($usuario['rol'] === 'profesor') {
        return true;
    }
    return $incidencia['usuario_id'] === $usuario['id'] && $incidencia['estado'] === 'abierta';
}

/** Guarda al usuario en la sesión tras un login correcto.
 * Se regenera el id de sesión para evitar la fijación de sesión (session fixation):
 * un atacante que conociera el id anterior no podría usarlo después de que la víctima inicie sesión
 * @param array $fila Fila del usuario (id, nombre, rol)
 * @return void
 */
function iniciarSesionUsuario(array $fila): void
{
    iniciarSesion();
    session_regenerate_id(true);
    // Solo se guarda lo necesario: nunca el hash de la contraseña
    $_SESSION['usuario'] = [
        'id'     => (int) $fila['id'],
        'nombre' => (string) $fila['nombre'],
        'rol'    => (string) $fila['rol'],
    ];
}

/** Cierra la sesión por completo: vacía los datos, borra la cookie del navegador y destruye la sesión del servidor
 * @return void
 */
function cerrarSesion(): void
{
    iniciarSesion();
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'],
    ]);
    session_destroy();
}

/** Convierte una fecha ISO 8601 en un texto legible (02/11/2026 18:30)
 * @param string $iso
 * @return string
 */
function formatearFecha(string $iso): string
{
    return (new DateTimeImmutable($iso))->format('d/m/Y H:i');
}
