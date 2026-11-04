<?php
/*
 * ============================================================================
 *  AulaFix · public/incidencia.php   (se usa como incidencia.php?id=3)
 * ============================================================================
 *  MÓDULO: 0613 · Servidor
 *  HITO:   Entrega final
 *  RA:     0613-RA4 (permisos comprobados en el servidor → 403)
 *          0613-RA6 (ver, cambiar estado con TRANSACCIÓN + historial, borrar)
 *
 *  ¿QUÉ ES ESTE FICHERO?
 *  El detalle de UNA incidencia con su historial de cambios. Además:
 *    · el profesorado puede CAMBIAR EL ESTADO dejando un comentario;
 *    · se puede BORRAR según los permisos.
 *
 * ----------------------------------------------------------------------------
 *  REQUISITOS (según el enunciado, apartados 3.1 y 3.2)
 * ----------------------------------------------------------------------------
 *  [ ] declare(strict_types=1); y comentario de cabecera.
 *  [ ] Solo con sesión iniciada.
 *  [ ] Leer y validar el id de la URL. Si no es válido o no existe → 404.
 *  [ ] Mostrar todos los datos de la incidencia y su historial (fecha, quién,
 *      estado anterior → nuevo, comentario). Todo con e().
 *  [ ] CAMBIAR ESTADO (solo profesor):
 *        · formulario POST + CSRF con el nuevo estado (lista blanca) y un comentario.
 *        · se actualiza la incidencia Y se añade una línea al historial
 *          EN UNA TRANSACCIÓN.
 *  [ ] BORRAR:
 *        · profesor → cualquier incidencia.
 *        · alumno   → solo las suyas y si siguen 'abierta'.
 *        · formulario POST + CSRF. Tras borrar → redirigir al listado con mensaje.
 *  [ ] Los botones solo se muestran a quien tiene permiso, PERO el servidor
 *      comprueba el permiso igualmente: si alguien envía el formulario a mano
 *      sin permiso → 403.
 *  [ ] PRG tras cada acción correcta.
 *
 * ----------------------------------------------------------------------------
 *  PISTAS
 * ----------------------------------------------------------------------------
 *  - Una sola página puede recibir DOS formularios distintos. Un campo oculto
 *    (por ejemplo name="accion" con valor "estado" o "borrar") os dice cuál
 *    se ha enviado. Usad un match o un switch para decidir.
 *  - El id llega como texto en $_GET. filter_input(INPUT_GET, 'id',
 *    FILTER_VALIDATE_INT) devuelve int, false o null. ¿Qué hacéis con cada caso?
 *  - Reutilizad puedeBorrar() de funciones.php para DOS cosas: decidir si se
 *    pinta el botón y comprobar el permiso al recibir el POST. Una sola regla.
 *  - El cambio de estado y el historial van en cambiarEstado() de db.php
 *    (con beginTransaction / commit / rollBack).
 *    PREGUNTA DE DEFENSA (Sobresaliente RA6): ¿qué pasaría sin la transacción?
 *  - ¿Tiene sentido "cambiar" al mismo estado que ya tiene? Decidid si lo
 *    permitís y validadlo.
 *  - Prueba del anexo: entrad como alumno, copiad el formulario de borrar de
 *    una incidencia ajena (o usad el inspector para crearlo) y enviadlo.
 *    El servidor debe responder 403.
 *  - Confirmación antes de borrar: un pequeño confirm() en JS es opcional; la
 *    seguridad NUNCA depende de él.
 *
 * ----------------------------------------------------------------------------
 *  TECNOLOGÍAS Y CONCEPTOS PARA INVESTIGAR
 * ----------------------------------------------------------------------------
 *  - filter_input + FILTER_VALIDATE_INT: https://www.php.net/manual/es/function.filter-input.php
 *  - http_response_code(403) y (404)
 *  - Expresión match (PHP 8): https://www.php.net/manual/es/control-structures.match.php
 *  - Transacciones PDO: https://www.php.net/manual/es/pdo.transactions.php
 *  - Control de acceso roto (OWASP Top 10 – A01 Broken Access Control)
 */

