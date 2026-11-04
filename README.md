<!--
============================================================================
 AulaFix · README.md
============================================================================
 MÓDULO: 0612 · Cliente & 0613 · Servidor
 HITO:   Hito 1 & Entrega final
 RA:     0613-RA1 · 0612-RA1 (mínimo obligatorio para aprobar: apartado 8.3)

 ¿QUÉ ES ESTE FICHERO?
 La guía de bienvenida y manual de puesta en marcha del proyecto. El anexo
 y los criterios mínimos exigen que cualquier evaluador pueda arrancar el
 proyecto en su ordenador siguiendo ÚNICAMENTE este documento.
============================================================================
-->

# AulaFix — Tablón de Incidencias del Aula

> Proyecto Intermodular para **0612 (Desarrollo Web en Entorno Cliente)** y **0613 (Desarrollo Web en Entorno Servidor)**  
> 2.º curso CFGS Desarrollo de Aplicaciones Web (DAW) · Comunidad de Madrid · LOMLOE-FP  
> **Autores:** [Nombre Alumno 1] y [Nombre Alumno 2]

---

## 1. Requisitos Previos

<!--
Describir qué software se necesita tener instalado en el equipo del evaluador:
- PHP 8.3 o superior (con extensiones pdo_mysql, mbstring).
- MySQL 8.0 o MariaDB equivalente.
- (Opcional) Docker y Docker Compose si se opta por arrancar con contenedores.
-->

## 2. Puesta en Marcha (Paso a Paso)

<!--
Explicar claramente cómo arrancar el proyecto:

Opción A: Entorno local nativo
1. Clonar el repositorio.
2. Configurar la base de datos en config.php (o mediante variables de entorno).
3. Instalar la base de datos y cargar los datos de prueba:
     php database/instalar.php
4. Iniciar el servidor embebido de PHP apuntando a la carpeta public/:
     php -S localhost:8000 -t public
5. Abrir en el navegador: http://localhost:8000

Opción B: Con Docker Compose (Sobresaliente 0613-RA1)
1. Ejecutar: docker compose up -d
2. Instalar datos: docker compose exec app php database/instalar.php
3. Acceder en http://localhost:8000 (y phpMyAdmin en http://localhost:8081)
-->

## 3. Usuarios de Prueba para la Corrección

<!--
OBLIGATORIO según el enunciado (apartado 7). Indicad credenciales reales
creadas por `database/instalar.php`:

| Rol       | Nombre           | Correo Electrónico       | Contraseña     |
|-----------|------------------|--------------------------|----------------|
| Profesor  | [Nombre Profesor]| profesor@aulafix.local    | ClaveSegura123 |
| Alumno    | [Nombre Alumno 1]| alumno1@aulafix.local     | ClaveSegura123 |
| Alumno    | [Nombre Alumno 2]| alumno2@aulafix.local     | ClaveSegura123 |
-->

## 4. Justificación Técnica de Decisiones de Diseño

<!--
OBLIGATORIO para optar a Sobresaliente en 0612-RA1 y 0613-RA1 / RA2:
1. JavaScript modular sin librerías ni frameworks:
   - ¿Por qué <script type="module">?
   - Carga diferida por defecto, ámbito aislado por archivo y reutilización de código mediante import/export.
2. Mejora Progresiva (Progressive Enhancement):
   - La aplicación genera en el servidor una tabla HTML completa mediante PHP. Si el navegador no tiene JavaScript activado, la web sigue siendo 100% funcional. Si JavaScript está disponible, lee la isla JSON (<script type="application/json">) y monta el panel interactivo.
3. Seguridad en la salida y datos:
   - Uso de función e() basada en htmlspecialchars(..., ENT_QUOTES, 'UTF-8') en toda salida HTML de PHP para prevenir XSS.
   - En JavaScript: uso estricto de textContent (nunca innerHTML) para datos de usuarios.
   - En JSON: json_encode con flags JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT para neutralizar inyecciones si una descripción contiene </script>.
   - Consultas con PDO y sentencias preparadas siempre (prevención de SQL Injection).
   - Protección contra CSRF con tokens de un solo uso en sesión y comparación en tiempo constante (hash_equals).
   - Patrón Post-Redirect-Get (PRG) con código HTTP 303 para evitar reenvíos accidentales con F5.
   - Contraseñas protegidas mediante password_hash con algoritmo Argon2id.
4. Organización de directorios y principio de menor privilegio:
   - Solo la carpeta public/ está expuesta al servidor web. Los archivos sensibles de configuración (config.php), lógica interna (includes/) y base de datos (database/) quedan inaccesibles desde Internet.
-->

## 5. Declaración sobre el uso de Herramientas de IA

<!--
OBLIGATORIO según el apartado 7 del enunciado:
"Si usáis herramientas de IA, decid para qué en el README. En la defensa se evalúa que entendáis vuestro código."

Detallad de forma transparente en qué habéis empleado la asistencia de IA:
- Generación del andamiaje (scaffolding) de archivos y plantillas de comentarios estructurados con los requisitos del proyecto.
- Asistencia en la resolución de dudas teóricas y sintaxis (Date/Intl, transacciones PDO, delegación de eventos).
- Todo el código funcional ha sido implementado, revisado y comprendido por los autores para su defensa oral.
-->

