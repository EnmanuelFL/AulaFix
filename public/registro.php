<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/registro.php
 * ============================================================================
 *  Página de registro: muestra el formulario (GET) y crea la cuenta (POST).
 *  Valida todo en el servidor, guarda la contraseña con Argon2id y usa CSRF + PRG.
 *  Las cuentas nuevas siempre son de alumno (el rol nunca viene del formulario).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';

// Si ya hay sesión, no tiene sentido registrarse otra vez
if (usuarioActual() !== null) {
    redirigir('incidencias.php');
}

$errores = [];
$datos = ['nombre' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf(leerPost('csrf'));

    $datos['nombre'] = leerPost('nombre');
    $datos['email']  = aMinusculas(leerPost('email'));
    // Las contraseñas NO se recortan con trim(): los espacios cuentan como carácter
    $clave  = (string) ($_POST['clave'] ?? '');
    $clave2 = (string) ($_POST['clave2'] ?? '');

    if (!longitudValida($datos['nombre'], 2, 80)) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 80 caracteres.';
    }

    if (!emailValido($datos['email'])) {
        $errores['email'] = 'Introduce un correo válido.';
    } elseif (buscarUsuarioPorEmail($datos['email']) !== null) {
        $errores['email'] = 'Ese correo ya está registrado.';
    }

    // Mínimo 10 caracteres con al menos una letra y un número (dos comprobaciones sencillas)
    if (longitudTexto($clave) < 10 || !preg_match('/[A-Za-z]/', $clave) || !preg_match('/\d/', $clave)) {
        $errores['clave'] = 'La contraseña debe tener al menos 10 caracteres, con letras y números.';
    }

    if ($clave2 !== $clave) {
        $errores['clave2'] = 'Las contraseñas no coinciden.';
    }

    if (empty($errores)) {
        try {
            // Argon2id: algoritmo lento a propósito para que sea muy costoso averiguar contraseñas por fuerza bruta
            crearUsuario($datos['nombre'], $datos['email'], password_hash($clave, PASSWORD_ARGON2ID));
            ponerFlash('Cuenta creada correctamente. Ya puedes iniciar sesión.');
            redirigir('login.php');
        } catch (PDOException $e) {
            // 23000 = violación de UNIQUE: otro registro con el mismo correo se coló justo antes que nosotros
            if ($e->getCode() === '23000') {
                $errores['email'] = 'Ese correo ya está registrado.';
            } else {
                throw $e;
            }
        }
    }
}

$titulo = 'Registro';
$scripts = [];
require_once __DIR__ . '/../includes/cabecera.php';
?>

<h1>Crear cuenta</h1>

<form method="post" action="">
    <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">

    <div>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="<?= e($datos['nombre']) ?>" autocomplete="name">
        <?php if (isset($errores['nombre'])): ?>
            <p class="error"><?= e($errores['nombre']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="email">Correo electrónico:</label><br>
        <input type="email" id="email" name="email" value="<?= e($datos['email']) ?>" autocomplete="email">
        <?php if (isset($errores['email'])): ?>
            <p class="error"><?= e($errores['email']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Las contraseñas nunca se devuelven al navegador, por eso estos campos no llevan value -->
    <div>
        <label for="clave">Contraseña (mínimo 10 caracteres, con letras y números):</label><br>
        <input type="password" id="clave" name="clave" autocomplete="new-password">
        <?php if (isset($errores['clave'])): ?>
            <p class="error"><?= e($errores['clave']) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="clave2">Repite la contraseña:</label><br>
        <input type="password" id="clave2" name="clave2" autocomplete="new-password">
        <?php if (isset($errores['clave2'])): ?>
            <p class="error"><?= e($errores['clave2']) ?></p>
        <?php endif; ?>
    </div>

    <button type="submit">Registrarme</button>
</form>

<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>
