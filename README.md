# AulaFix — Tablón de Incidencias del Aula

**Autores:** Enmanuel y Germán  
**Ciclo:** 2.º CFGS Desarrollo de Aplicaciones Web (DAW) · Módulos 0612 (Cliente) y 0613 (Servidor)

---

## 🎯 Finalidad del proyecto

**AulaFix** es una aplicación web diseñada para gestionar las incidencias del aula de informática (ordenadores que no arrancan, proyectores averiados, problemas de red o software, impresoras sin tóner, etc.) evitando que los avisos se pierdan al comentarse de palabra.

* **Alumnado:** inicia sesión, consulta el estado de las incidencias y registra nuevos avisos.
* **Profesorado:** gestiona las incidencias, cambia su estado (`abierta` ➔ `en_curso` ➔ `resuelta`) dejando comentarios en el historial y puede eliminar registros.
* **Panel interactivo:** muestra tarjetas de resumen en tiempo real, filtros por estado y prioridad, buscador instantáneo y tabla ordenable sin perder la compatibilidad si JavaScript está desactivado.

---

## 🛠️ Tecnologías utilizadas

![Tecnologías utilizadas](https://skillicons.dev/icons?i=php,mysql,js,html,css,docker,git)

* **Servidor (0613):** PHP 8.3 (tipado estricto, PDO, Argon2id, sesiones seguras, CSRF y PRG) + MySQL 8.
* **Cliente (0612):** HTML5, CSS3 y JavaScript modular nativo (ES Modules, DOM, `localStorage`, `Intl`), sin librerías ni frameworks.
* **Entorno y control de versiones:** Docker Compose y Git.

---

## 🚀 Cómo ejecutar el proyecto en cualquier ordenador

### Opción 1 · Con Docker (recomendada)

1. Clona el repositorio y entra en la carpeta:
   ```bash
   git clone https://github.com/EnmanuelFL/AulaFix.git
   cd AulaFix
   ```
2. Levanta los contenedores (PHP 8.3 + Apache, MySQL 8 y phpMyAdmin):
   ```bash
   docker compose up -d --build
   ```
3. Crea las tablas y carga los datos de ejemplo:
   ```bash
   docker compose exec app php database/instalar.php
   ```
4. Abre en el navegador:
   * **Aplicación:** http://localhost:8080
   * **phpMyAdmin:** http://localhost:8081 (servidor `db`, usuario `aulafix`, contraseña `aulafix`)

### Opción 2 · En local (con PHP 8.3+ y MySQL 8 instalados)

1. Clona el repositorio y entra en la carpeta `AulaFix`.
2. Si tu MySQL local usa usuario/clave distintos a `root` sin contraseña, crea un archivo `.env` en la raíz con:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=aulafix
   DB_USER=tu_usuario
   DB_PASS=tu_contraseña
   ```
3. Crea la base de datos y los datos de prueba:
   ```bash
   php database/instalar.php
   ```
4. Arranca el servidor integrado de PHP apuntando a la carpeta pública:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Abre **http://localhost:8000** en tu navegador.

---

## 👤 Usuarios de prueba

Todos los usuarios iniciales creados por `database/instalar.php` comparten la contraseña **`AulaFix2026`**:

| Rol | Nombre | Correo | Contraseña |
| :--- | :--- | :--- | :--- |
| **Profesor** | Pablo Profesor | `profesor@aulafix.local` | `AulaFix2026` |
| **Alumno** | Lucía Alumna | `lucia@aulafix.local` | `AulaFix2026` |
| **Alumno** | Marcos Alumno | `marcos@aulafix.local` | `AulaFix2026` |

---

## 🌿 Uso de ramas en Git

* **`main`**: Rama principal con la versión estable y final lista para entrega y defensa.
* **`dev`**: Rama de integración donde se unifican y prueban conjuntamente los avances de Servidor y Cliente antes de pasar a `main`.
* **`dev-enma`**: Rama de desarrollo personal para la parte de Servidor (PHP, base de datos, sesiones, seguridad y Docker).
* **`dev-german`**: Rama de desarrollo personal para la parte de Cliente (módulos JavaScript, manipulación del DOM y estilos CSS).
