<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · includes/funciones.php
 * ============================================================================
 *  Utilidades compartidas: escapado XSS (e), JSON seguro, sesión segura,
 *  tokens CSRF, redirecciones PRG, mensajes flash, validaciones y permisos.
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
