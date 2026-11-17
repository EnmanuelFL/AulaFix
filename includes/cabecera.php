<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · includes/cabecera.php
 * ============================================================================
 *  Cabecera común HTML5, barra de navegación adaptada a la sesión y mensajes
 *  flash. Se incluye al inicio de cada página tras ejecutar su lógica PHP.
 */
 $titulo = $titulo ?? 'AulaFix';
 $flash = obtenerFlash();
 $usuarioCabecera = usuarioActual(); // null si no hay sesión: decide qué menú se pinta
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=e($titulo)?> . AulaFix</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <span><strong>AulaFix</strong></span>
        <nav class="navbar">
            <ul class="navbar-list">
                <?php if ($usuarioCabecera !== null): ?>
                    <li><a href="incidencias.php">Incidencias</a></li>
                    <li><a href="nueva.php">Nueva Incidencia</a></li>
                    <li><?= e($usuarioCabecera['nombre']) ?></li>
                    <!-- Cerrar sesión es un formulario POST con token CSRF, no un enlace: así nadie puede cerrártela desde otra web -->
                    <li>
                        <form method="post" action="logout.php">
                            <input type="hidden" name="csrf" value="<?= e(tokenCsrf()) ?>">
                            <button type="submit">Cerrar sesión</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php if($flash !== null): ?>
            <p class="mensaje-flash"><?=e($flash)?></p>
        <?php endif; ?>

    

