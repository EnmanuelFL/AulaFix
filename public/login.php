<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/login.php
 * ============================================================================
 *  Inicio de sesión: comprueba correo y contraseña con password_verify(), regenera el id de
 *  sesión al entrar y, si se marca la casilla, recuerda SOLO el correo en una cookie 30 días.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';

const COOKIE_EMAIL = 'recordar_email';

if (usuarioActual() !== null) {
    redirigir('incidencias.php');
}

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$errores = [];

// Si el navegador recuerda el correo, el campo aparece relleno (solo si el valor es un correo válido)
$recordado = (string) ($_COOKIE[COOKIE_EMAIL] ?? '');
$datos = ['email' => emailValido($recordado) ? $recordado : '', 'recordar' => $recordado !== ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf(leerPost('csrf'));

    $datos['email']    = aMinusculas(leerPost('email'));
    $datos['recordar'] = isset($_POST['recordar']);
    $clave = (string) ($_POST['clave'] ?? '');

    $usuario = buscarUsuarioPorEmail($datos['email']);

    // MISMO mensaje si el correo no existe o si la contraseña es incorrecta:
    // así nadie puede averiguar qué correos están registrados (enumeración de usuarios)
    if ($usuario === null || !password_verify($clave, $usuario['clave_hash'])) {
        $errores['general'] = 'Correo o contraseña incorrectos.';
    } else {
        iniciarSesionUsuario($usuario); // aquí se regenera el id de sesión

        if ($datos['recordar']) {
            // Cookie con el correo (nunca la contraseña): HttpOnly impide leerla desde JavaScript
            // y SameSite=Lax evita que se envíe en peticiones desde otras webs
            setcookie(COOKIE_EMAIL, $datos['email'], [
                'expires'  => time() + COOKIES_RECORDAR_DIAS * 24 * 60 * 60,
                'path'     => '/',
                'secure'   => $https,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } elseif (isset($_COOKIE[COOKIE_EMAIL])) {
            // Si ya no quiere que se recuerde, se borra enviándola con una fecha pasada
            setcookie(COOKIE_EMAIL, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'secure'   => $https,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        ponerFlash('Bienvenido/a, ' . $usuario['nombre'] . '.');
        redirigir('incidencias.php');
    }
}

$titulo = 'Iniciar sesión';
$scripts = [];
require_once __DIR__ . '/../includes/cabecera.php';
?>

<h1>Iniciar sesión</h1>

<?php if (isset($_GET['salida'])): ?>
    <p class="mensaje-flash">Has cerrado la sesión.</p>
<?php endif; ?>

<?php if (isset($errores['general'])): ?>
    <p class="error"><?= e($errores['general']) ?></p>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

    <div>
        <label for="email">Correo electrónico:</label><br>
        <input type="email" id="email" name="email" value="<?= e($datos['email']) ?>" autocomplete="email">
    </div>

    <div>
        <label for="clave">Contraseña:</label><br>
        <input type="password" id="clave" name="clave" autocomplete="current-password">
    </div>

    <div>
        <label>
            <input type="checkbox" name="recordar" <?= $datos['recordar'] ? 'checked' : '' ?>>
            Recordar mi correo
        </label>
    </div>

    <button type="submit">Entrar</button>
</form>

<p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>
