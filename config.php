<?php declare(strict_types=1);
/*
 * ============================================================================
 *  AulaFix · config.php
 * ============================================================================
 *  Configuración central de la aplicación: datos de conexión a MySQL (con
 *  variables de entorno / .env) y listas blancas de valores permitidos.
 */

// Zona horaria de la aplicación: PHP y MySQL deben usar la misma para que las fechas coincidan
date_default_timezone_set('Europe/Madrid');

// En local se leen los datos de conexión de .env (que NO se sube a Git por el .gitignore)
// Si el fichero no existe (Docker, ordenador del profesor), $env queda vacío
$env = file_exists(__DIR__ . '/.env') ? (parse_ini_file(__DIR__ . '/.env') ?: []) : [];

/**
 * Devuelve un valor de configuración.
 * Prioridad: variable de entorno real (Docker) > fichero .env > valor por defecto.
 * Así el mismo código funciona en cualquier equipo sin tocar nada.
 *
 * @param string $clave      Nombre de la variable (ej. DB_HOST).
 * @param string $porDefecto Valor que se usa si no existe en ningún sitio.
 * @param array  $env        Contenido del .env ya leído.
 * @return string
 */
function valorEntorno(string $clave, string $porDefecto, array $env): string
{
    $real = getenv($clave);
    if ($real !== false) {
        return $real;
    }
    return isset($env[$clave]) ? (string) $env[$clave] : $porDefecto;
}

// Datos de conexión a MySQL (nunca se escriben contraseñas reales en el código)
define('DB_HOST', valorEntorno('DB_HOST', '127.0.0.1', $env));
define('DB_PORT', valorEntorno('DB_PORT', '3306', $env));
define('DB_NAME', valorEntorno('DB_NAME', 'aulafix', $env));
define('DB_USER', valorEntorno('DB_USER', 'root', $env));
define('DB_PASS', valorEntorno('DB_PASS', '', $env));
define('DB_CHARSET', 'utf8mb4'); // utf8mb4 admite tildes, ñ y emojis (utf8 de MySQL no)

// Listas blancas: los únicos valores que acepta la aplicación (coinciden con los ENUM de MySQL)
const CATEGORIAS = ['hardware', 'software', 'red', 'otro'];
const PRIORIDADES = ['baja', 'media', 'alta'];
const ESTADOS = ['abierta', 'en_curso', 'resuelta'];
const ROLES = ['alumno', 'profesor'];

// Días que dura la cookie «Recordar mi correo»
const COOKIES_RECORDAR_DIAS = 30;
