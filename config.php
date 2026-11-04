<?php
/*
 * ============================================================================
 *  AulaFix · config.php
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Hito 1 (valores permitidos) + Entrega final (datos de conexión a MySQL)
 *  RA:     0613-RA1 (configuración en un único fichero) · 0613-RA6 (utf8mb4,
 *          datos en config.php o en variables de entorno)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  La configuración central de la aplicación. Es el ÚNICO sitio donde
 *  aparecen los datos de conexión a MySQL y las listas de valores permitidos.
 *  Está FUERA de public/, así que el navegador nunca puede pedirlo.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado)
 * ----------------------------------------------------------------------------
 *  [ ] Primera instrucción: declare(strict_types=1);  (obligatorio en TODOS los .php)
 *  [ ] Comentario de cabecera de 1-2 líneas: qué hace este fichero.
 *  [ ] Datos de conexión: servidor (host), puerto, base de datos, usuario y contraseña.
 *  [ ] Esos datos pueden venir de VARIABLES DE ENTORNO para no subir contraseñas
 *      al repositorio, con un valor por defecto para trabajar en local.
 *  [ ] Charset de la conexión: utf8mb4.
 *  [ ] Valores permitidos (listas blancas) para validar y para el SQL:
 *        categoria → hardware · software · red · otro
 *        prioridad → baja · media · alta
 *        estado    → abierta · en_curso · resuelta
 *        rol       → alumno · profesor
 *  [ ] (Recomendado) Duración de la cookie «Recordar mi correo»: 30 días.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - getenv('DB_HOST') devuelve false si la variable no existe. ¿Qué operador
 *    os deja poner un valor por defecto en una sola expresión?
 *    Investigad la diferencia entre ?: y ?? cuando el valor es false.
 *    (Hay un ejemplo en los apuntes: Unidad5PHP/6_ArquitecturaEnCapas.php)
 *  - Decidid cómo exponer la configuración: constantes con const / define(),
 *    o un fichero que devuelve un array con return [...]. Las constantes
 *    pueden ser arrays desde PHP 5.6 (const PRIORIDADES = [...]).
 *  - Si los valores permitidos viven aquí, el formulario (nueva.php), las
 *    validaciones (funciones.php) y el instalador usan la MISMA lista:
 *    solo hay un sitio que cambiar.
 *  - En Docker, el servidor MySQL no está en 127.0.0.1, sino en el nombre del
 *    servicio (por ejemplo "db"). Con variables de entorno, el mismo
 *    config.php sirve en vuestro PC y en Docker sin tocar nada.
 *  - En la defensa os pueden preguntar POR QUÉ no se suben las contraseñas a
 *    Git. Tened la respuesta preparada (y comentadla aquí).
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - getenv(): https://www.php.net/manual/es/function.getenv.php
 *  - Constantes (const y define): https://www.php.net/manual/es/language.constants.php
 *  - Operadores ?: y ??: https://www.php.net/manual/es/language.operators.comparison.php
 *  - Variables de entorno en Docker Compose (clave environment:)
 *  - ¿Por qué utf8mb4 y no utf8 en MySQL? (caracteres de 4 bytes, emojis)
 */

