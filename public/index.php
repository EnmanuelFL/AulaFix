<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/index.php
 * ============================================================================
 *  Punto de entrada de la aplicación: redirige al listado si hay sesión
 *  iniciada o a la página de inicio de sesión en caso contrario.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';

// Con sesión iniciada se va al listado y sin sesión al login
if (usuarioActual() !== null) {
    redirigir('incidencias.php');
}
redirigir('login.php');
