# Gimnasia y Tiro de Salta | Sitio Oficial del Hincha

Sitio web oficial del hincha del **Club Atlético Gimnasia y Tiro de Salta** desarrollado con arquitectura PHP Server-Side Rendering (SSR), modularización por plantillas (SSI), variables de entorno (.env), base de datos relacional MySQL y sistema de autenticación seguro basado en sesiones y control de acceso por roles (**OWASP**).

---

## 🔐 Trabajo Práctico N° 8 — Persistencia de Sesión, Hashing Seguro y Control de Acceso (RBAC)

> **Documentación Completa del TP8:** Consulta la carpeta [`tp8/`](./tp8/) y su guía técnica en [`tp8/README.md`](./tp8/README.md).

### 1. Objetivos del Trabajo Práctico N° 8
1. **Persistencia de estado con sesiones seguras:** Implementación de `$_SESSION` con cookies seguras (`HttpOnly`, `SameSite=Strict`, `Secure` en HTTPS).
2. **Módulo de Registro, Login y Logout:** Flujo completo de autenticación conectado a MySQL mediante PDO con consultas preparadas.
3. **Hashing seguro según OWASP:** Encriptación con `password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12])` y validación con `password_verify()`.
4. **Protección de rutas y control por roles:** Script reutilizable `auth_guard.php` que custodia vistas privadas y restringe accesos por rol (`admin` y `usuario`).

### 2. Archivos Implementados del TP8
- **[`auth_guard.php`](./auth_guard.php) / [`includes/auth_guard.php`](./includes/auth_guard.php):** Guardián de rutas reutilizable con funciones `verificar_autenticacion()` y `verificar_rol('admin')`.
- **[`registro.php`](./registro.php):** Registro de nuevos socios con sanitización estricta anti-XSS (`htmlspecialchars`, `strip_tags`), validación de correo único y hashing BCRYPT.
- **[`login.php`](./login.php):** Inicio de sesión con verificación de hash, invocación de `session_regenerate_id(true)` para mitigar **Session Fixation** y guardado seguro de variables `$_SESSION`.
- **[`logout.php`](./logout.php):** Cierre seguro: vaciado de `$_SESSION`, eliminación de cookies del navegador con timestamp en el pasado y ejecución de `session_destroy()`.
- **[`dashboard.php`](./dashboard.php):** **Vista Protegida 1:** Panel del Hincha y Carnet Digital de Socio (accesible para cualquier usuario autenticado).
- **[`admin_panel.php`](./admin_panel.php):** **Vista Protegida 2:** Panel de Administración Albo (exclusivo para usuarios con rol `admin`).
- **[`tp8/schema_usuarios.sql`](./tp8/schema_usuarios.sql):** Script DDL y DML de la tabla `usuarios` en MySQL.
- **[`tp8/preparar_entrega_git_tp8.bat`](./tp8/preparar_entrega_git_tp8.bat):** Script automatizado para preparar ramas y commits atómicos del TP8 en Git.

### 3. Credenciales de Prueba para Evaluación Docente

| Rol | Correo Electrónico | Contraseña | Destino tras Login |
|---|---|---|---|
| **Administrador** | `franco.guantay@gyt.com.ar` | `Admin123!` | Redirige a [`admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php) |
| **Socio / Usuario** | `agustin.perez@prensaalbo.com.ar` | `Socio123!` | Redirige a [`dashboard.php`](http://localhost/gyt-nuevo/dashboard.php) |
| **Nuevo Socio** | Registrarse en [`registro.php`](http://localhost/gyt-nuevo/registro.php) | `(mínimo 8 car.)` | Redirige a [`login.php`](http://localhost/gyt-nuevo/login.php) |

---

## Trabajo Práctico N° 4 — Migración a PHP, Modularización (SSI) y Renderizado Server-Side (SSR)

### 1. Título y Descripción del Proyecto
- **Asignatura:** Programación Web (Unidad 4 – Entorno Servidor y Primeros Pasos con PHP).
- **Autor:** Franco Guantay.
- **Resumen de la Refactorización:**
  Se transformó la maqueta estática previa (Hito 1 en HTML) en una aplicación web dinámica basada en **PHP 8**. Se implementó la técnica de **Server Side Includes (SSI)** mediante la extracción de componentes reutilizables (`header.php`, `nav.php`, `footer.php`), eliminando la duplicación de código en todas las vistas. Se sentaron las bases de **Server-Side Rendering (SSR)** mediante la inyección dinámica de títulos (`$titulo_pagina`), control dinámico del enlace activo en el menú (`$pagina_actual` con clase `.active`), y gestión de configuración centralizada con variables de entorno (`.env`).

### 2. Tecnologías Utilizadas
- **HTML5 Semántico:** Estructura modular de pantallas y accesibilidad.
- **CSS3 Personalizado & Responsive Design:** Variables CSS, maquetación flexible (Flexbox y CSS Grid), modo oscuro y soporte multidispositivo.
- **JavaScript (ES6):** Reloj en tiempo real, interactividad de menú hamburguesa, visor modal de imágenes y validación en cliente.
- **PHP 8.x:** Procesamiento del lado del servidor (SSR), inclusión de plantillas (SSI con `require_once`), validaciones y sanitización con `htmlspecialchars()`.
- **Servidor Web Local:** XAMPP (Apache 2.4 + PHP 8 + MySQL/MariaDB).
- **Gestión de Entorno:** Archivo `.env` privado y plantilla `.env.example` versionada.
- **Control de Versiones:** Git y GitHub con flujo de ramas (`feature/migracion-php-ssi` integrada en `main`).

### 3. Instrucciones de Instalación y Ejecución Local
1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/fguantay39-dot/GyTSalta.git
   ```
2. **Copiar a la carpeta del servidor local:**
   Ubicar la carpeta del proyecto dentro del directorio raíz de XAMPP:
   ```
   C:\xampp\htdocs\gyt-nuevo\
   ```
3. **Configurar las variables de entorno:**
   Copiar el archivo de plantilla `.env.example` y renombrarlo como `.env`:
   ```bash
   copy .env.example .env
   ```
4. **Iniciar el servidor local:**
   - Abrir el panel de control de **XAMPP**.
   - Iniciar el servicio **Apache** y **MySQL**.
5. **Rutas principales de la aplicación:**
   - **Inicio / Portada Principal:** [`http://localhost/gyt-nuevo/`](http://localhost/gyt-nuevo/)
   - **Noticias:** [`http://localhost/gyt-nuevo/noticias.php`](http://localhost/gyt-nuevo/noticias.php)
   - **Plantel Oficial:** [`http://localhost/gyt-nuevo/plantel.php`](http://localhost/gyt-nuevo/plantel.php)
   - **Partidos y Tabla:** [`http://localhost/gyt-nuevo/partidos.php`](http://localhost/gyt-nuevo/partidos.php)
   - **Historia y Estadio:** [`http://localhost/gyt-nuevo/historia.php`](http://localhost/gyt-nuevo/historia.php)
   - **Contacto Institucional:** [`http://localhost/gyt-nuevo/contacto.php`](http://localhost/gyt-nuevo/contacto.php)
   - **Iniciar Sesión (TP8):** [`http://localhost/gyt-nuevo/login.php`](http://localhost/gyt-nuevo/login.php)
   - **Registro de Socios (TP8):** [`http://localhost/gyt-nuevo/registro.php`](http://localhost/gyt-nuevo/registro.php)
   - **Mi Panel / Carnet Digital (TP8 - Protegido):** [`http://localhost/gyt-nuevo/dashboard.php`](http://localhost/gyt-nuevo/dashboard.php)
   - **Panel Administrador (TP8 - Solo Admin):** [`http://localhost/gyt-nuevo/admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php)

---

## Clase 5 — Formularios y seguridad XSS

El desarrollo está dentro de la carpeta `clase5`.
- **POST — Contacto:** procesa nombre, correo y mensaje en el servidor con validaciones.
- **GET — Búsqueda:** filtra novedades por palabra clave o categoría.
- **Seguridad:** normalización, validación y escape con `htmlspecialchars()`.

---

## TP6 — Base de Datos Relacional, Normalización y Consultas SQL

Toda la documentación, diagramas y scripts de base de datos se encuentran en la carpeta [`tp6/`](./tp6/):
- **[NORMALIZACION.md](./tp6/NORMALIZACION.md):** 1FN, 2FN, 3FN y modelo relacional.
- **[schema.sql](./tp6/schema.sql):** Script DDL con claves foráneas e índices.
- **[consultas.sql](./tp6/consultas.sql):** Consultas DML de prueba (CRUD).
