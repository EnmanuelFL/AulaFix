<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · public/logout.php
 * ============================================================================
 *  Cierra la sesión. No pinta HTML: solo acepta el formulario POST con token CSRF del botón
 *  «Cerrar sesión» (cabecera.php). Va por POST para que una web ajena no pueda cerrarte la
 *  sesión con un simple enlace o imagen (<img src="logout.php">).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/funciones.php';

// Si no llega por POST, se rechaza con 405 (método no permitido)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

verificarCsrf(leerPost('csrf'));

// Cierre completo: vaciar datos + borrar cookie de sesión + destruir sesión (ver funciones.php)
// No se borra la cookie «Recordar mi correo»: precisamente sirve para la próxima vez
cerrarSesion();
redirigir('login.php?salida=1');
