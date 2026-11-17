# AulaFix — Tablón de Incidencias del Aula

Proyecto Intermodular para **0612 (Desarrollo Web en Entorno Cliente)** y **0613 (Desarrollo Web en Entorno Servidor)**  
2.º curso CFGS Desarrollo de Aplicaciones Web (DAW) · Comunidad de Madrid · LOMLOE-FP  
**Autores:** Enmanuel y Germán

---

## 1. Requisitos Previos

* **Opción local:** PHP 8.3+ (con `pdo_mysql`), MySQL 8.0+.
* **Opción Docker:** Docker y Docker Compose (no requiere instalar PHP ni MySQL en el equipo).

---

## 2. Puesta en Marcha

### Opción A · Con Docker (recomendada)

1. Clonar el repositorio y entrar en la carpeta del proyecto:
   ```bash
   git clone https://github.com/EnmanuelFL/AulaFix.git
   cd AulaFix
   ```
2. Levantar los contenedores (PHP 8.3 + Apache, MySQL 8.0 y phpMyAdmin):
   ```bash
   docker compose up -d --build
   ```
3. Crear las tablas y cargar los datos de ejemplo:
   ```bash
   docker compose exec app php database/instalar.php
   ```
4. Abrir en el navegador:
   * Aplicación: **http://localhost:8080**
   * phpMyAdmin: **http://localhost:8081** (servidor `db`, usuario `aulafix`, contraseña `aulafix`)

### Opción B · En local (PHP + MySQL instalados en el equipo)

1. Configurar los datos de conexión en un fichero `.env` (opcional, no se sube a Git) o mediante variables de entorno (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`). Por defecto conecta a `127.0.0.1:3306`, BD `aulafix`, usuario `root`, clave vacía.
2. Crear las tablas y datos de ejemplo:
   ```bash
   php database/instalar.php
   ```
3. Arrancar el servidor integrado de PHP apuntando únicamente a `public/`:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Abrir **http://localhost:8000** en el navegador.

---

## 3. Usuarios de Prueba

Todos los usuarios creados por `database/instalar.php` usan la contraseña **`AulaFix2026`**:

| Rol | Nombre | Correo electrónico | Contraseña |
| :--- | :--- | :--- | :--- |
| **Profesor** | Pablo Profesor | `profesor@aulafix.local` | `AulaFix2026` |
| **Alumno** | Lucía Alumna | `lucia@aulafix.local` | `AulaFix2026` |
| **Alumno** | Marcos Alumno | `marcos@aulafix.local` | `AulaFix2026` |

---

## 4. Decisiones Técnicas y Seguridad

* **Carpeta `public/` como única raíz accesible:** Tanto Apache en Docker como `php -S -t public` exponen únicamente `public/`. De este modo `config.php`, `includes/` y `database/` quedan fuera del alcance del navegador.
* **Configuración centralizada sin contraseñas en Git:** `config.php` prioriza variables de entorno (`getenv()`), después `.env` local (ignorado en `.gitignore`) y por último valores por defecto. Así el mismo código funciona en local, en Docker y en el equipo del profesorado.
* **Acceso a BD con PDO y sentencias preparadas:** Toda consulta usa `prepare()` y `execute()` con `ATTR_EMULATE_PREPARES => false` y `utf8mb4` para prevenir inyecciones SQL. El cambio de estado y el registro en `historial` van dentro de una **transacción** (`beginTransaction`, `FOR UPDATE`, `commit`, `rollBack`) para evitar estados inconsistentes.
* **Prevención de XSS y JSON seguro:** Toda salida HTML en PHP pasa por `e()` (`htmlspecialchars` con `ENT_QUOTES` y `UTF-8`) al mostrar (no al guardar, para no corromper los datos originales). El JSON incrustado para el cliente usa `json_encode` con `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT` para neutralizar etiquetas como `</script>`.
* **Autenticación, sesiones y CSRF:** Contraseñas con `PASSWORD_ARGON2ID`, `session_regenerate_id(true)` al iniciar sesión (contra fijación de sesión), cookies con `HttpOnly` y `SameSite=Lax`, mismo mensaje de error en login (evita enumeración de usuarios), tokens CSRF comprobados con `hash_equals()`, patrón PRG (`303`) en todos los POST y validación de permisos en servidor (`403`).
* **Cliente modular sin librerías y mejora progresiva:** JavaScript organizado en módulos ES (`<script type="module">`) sin frameworks. Si JavaScript está desactivado, la tabla HTML generada por PHP sigue funcionando.

---

## 5. Declaración de Uso de Herramientas de IA

Durante el desarrollo de este proyecto se ha utilizado asistencia de IA para:
* Generar la estructura inicial de carpetas y plantillas de documentación/comentarios de cada archivo.
* Apoyo y tutoría paso a paso en la sintaxis de PHP 8.3 (PDO, transacciones, sesiones, CSRF/PRG), configuración de Docker y resolución de errores de entorno.
* Todo el código ha sido revisado, probado y comprendido por los autores para su defensa oral.
