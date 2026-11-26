# Club Atlético Gimnasia y Tiro de Salta | Sitio Oficial del Hincha

Repositorio oficial del proyecto web del **Club Atlético Gimnasia y Tiro de Salta** ("El Albo"), desarrollado como parte de la trayectoria de formación en **Programación Web** en la **EET N.º 3139 Gral. Martín Miguel de Güemes** (Salta, Argentina).

> **Alumno:** Franco Guantay  
> **Institución:** Escuela de Educación Técnica N.º 3139 Gral. Martín Miguel de Güemes  
> **Materia:** Programación Web  
> **Repositorio GitHub:** [https://github.com/fguantay39-dot/GyTSalta.git](https://github.com/fguantay39-dot/GyTSalta.git)  
> **Entorno de Ejecución:** Apache 2.4 / PHP 8.x / MySQL 8.x (XAMPP Server)

---

## 📋 Resumen de la Trayectoria de Desarrollo

Este proyecto documenta la evolución integral de una aplicación web, desde su maquetación estática inicial hasta convertirse en una plataforma dinámica con arquitectura **Server-Side Rendering (SSR)** en PHP, base de datos relacional MySQL normalizada y un sistema robusto de autenticación, seguridad **OWASP** y **Control de Acceso Basado en Roles (RBAC)** con capacidades de CMS en vivo:

```
[Hito 1: Maquetación HTML5/CSS3/JS] 
       │
       ▼
[TP 4: Migración a PHP 8 + Modularización SSI + SSR + .env]
       │
       ▼
[Clase 5: Formularios POST/GET + Sanitización y Protección XSS]
       │
       ▼
[TP 6: Base de Datos Relacional MySQL + Normalización 1FN, 2FN, 3FN]
       │
       ▼
[TP 8: Sesiones Seguras + BCRYPT (OWASP) + RBAC + CMS de Administración Total]
```

---

## 🔐 Módulo de Administración Total (CMS) y Roles de Acceso (TP8)

El sitio cuenta con una diferenciación completa entre dos tipos de experiencias:
1. **Usuario / Hincha Común:** Diseñado exclusivamente para navegar, leer noticias, consultar el plantel profesional, revisar el fixture y tabla de posiciones, conocer la historia del club, ver fotos y enviar mensajes de contacto. No tiene permisos para modificar ningún contenido. Dispone de un carnet digital de socio en su panel personal.
2. **Administrador (`fguantay39@gmail.com`):** Posee facultades para **editar la totalidad del sitio web** directamente desde la interfaz, sin necesidad de tocar código fuente.

### 🔑 Credenciales de Prueba para Evaluación

| Rol | Correo Electrónico | Contraseña | Permisos y Capacidades | Destino tras Login |
|---|---|---|---|---|
| **Administrador Oficial** | `fguantay39@gmail.com` | `Admin123!` | **Edición Total:** Portada, Noticias, Historia, Galería, Plantel, Fixture, Tabla y Gestión de Roles | Barra de Admin + Edición In-Situ + [`admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php) |
| **Administrador Alternativo** | `franco.guantay@gyt.com.ar` | `Admin123!` | Mismos privilegios de administración y edición total | [`admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php) |
| **Socio / Usuario Común** | `agustin.perez@prensaalbo.com.ar` | `Socio123!` | **Solo Navegación:** Lectura de notas, consulta de plantel/tabla y envío de consultas | Redirige a [`dashboard.php`](http://localhost/gyt-nuevo/dashboard.php) (Carnet Digital) |
| **Nuevo Hincha** | Registrarse en [`registro.php`](http://localhost/gyt-nuevo/registro.php) | Clave propia (≥ 8 caracteres) | Rol automático de `usuario` común | Redirige al inicio de sesión |

---

### 🛠️ Herramientas de Edición Disponibles para el Administrador

Cuando se inicia sesión con la cuenta de administrador (`fguantay39@gmail.com` o `franco.guantay@gyt.com.ar`), el sitio habilita dinámicamente:

1. **Barra Superior de Estado en Tiempo Real (en todas las páginas):**
   - Presente en el encabezado (`includes/header.php`), indicando el rol activo (**Administrador**, **Usuario/Socio** o **Invitado**).
   - Para el admin, muestra acceso directo al Panel de Control y enlace rápido para cerrar sesión.

2. **Portada Principal (`index.php`):**
   - **Barra de Gestión:** Acceso a métricas del portal.
   - **Publicador de Noticias en Vivo:** Formulario desplegable para redactar y publicar noticias de inmediato con categoría, título, bajada y contenido completo guardado en MySQL.
   - **Edición y Eliminación In-Situ:** Cada noticia muestra botones para editar el texto o darla de baja al instante.
   - **Gestor de Roles de Usuarios:** Permite al admin ascender usuarios a `admin` o degradarlos a `usuario` desde la misma portada.

3. **Historia y Estadio (`historia.php`):**
   - **Editor de Secciones:** Permite editar los textos de la historia del club (Orígenes de 1902, Grandes Ascensos, Copa Argentina) y los detalles del Estadio Monumental Gigante del Norte.
   - **Gestor de Galería Fotográfica:** Formulario para subir nuevas postales históricas (con título, descripción y URL de imagen) y botón de eliminación para cada foto.

4. **Plantel Profesional (`plantel.php`):**
   - **Alta de Futbolistas:** Formulario para agregar jugadores con nombre, dorsal, posición (Arquero, Defensor, Volante, Delantero), partidos jugados, goles y fotografía.
   - **Edición y Baja de Jugadores:** Modificación de estadísticas de rendimiento o baja del jugador en tiempo real.

5. **Fixture y Tabla de Posiciones (`partidos.php`):**
   - **Editor de Partidos:** Agregar nuevos encuentros al fixture con fecha, horario, rival, condición (Local/Visitante), estado y resultado.
   - **Editor de Tabla de Posiciones:** Actualización directa de los puntos (Pts) y partidos jugados (PJ) de los equipos de la Primera Nacional.

6. **Noticias Albo (`noticias.php`):**
   - Edición completa de títulos, bajadas, categorías y eliminación de artículos.

7. **Buzón de Contacto (`contacto.php`):**
   - Los hinchas envían consultas mediante el formulario; el administrador tiene una sección privada para leer todos los mensajes recibidos y eliminarlos tras su respuesta.

8. **Panel de Control Central (`admin_panel.php`):**
   - Dashboard consolidado con métricas de usuarios registrados, publicaciones activas, estadísticas del plantel y accesos directos de mantenimiento.

---

## 📚 Detalle de Trabajos Prácticos y Módulos Desarrollados

### 1. Trabajo Práctico N° 8 — Persistencia de Sesión, Hashing Seguro (OWASP) y RBAC
*Ubicación detallada:* [`tp8/README.md`](./tp8/README.md) y [`tp8/schema_usuarios.sql`](./tp8/schema_usuarios.sql)
- **Persistencia de Sesión Segura:** Empleo de variables superglobales `$_SESSION` con cookies seguras configuradas con atributos `HttpOnly` (previene robo por scripts XSS), `SameSite=Strict` (protección contra CSRF) y expiración controlada.
- **Mitigación de Session Fixation:** Invocación inmediata de `session_regenerate_id(true)` tras validar las credenciales en el login para invalidar identificadores antiguos.
- **Cifrado Fuerte de Contraseñas:** Algoritmo **BCRYPT** (`PASSWORD_BCRYPT` con costo 12) mediante `password_hash()`, cumpliendo las directrices de ciberseguridad de **OWASP**. Validación con `password_verify()`.
- **Guardián de Rutas Reutilizable (`auth_guard.php`):** Funciones `verificar_autenticacion()` y `verificar_rol('admin')` que blindan el acceso a las vistas privadas del sistema.
- **Cierre Seguro (`logout.php`):** Vaciado de variables (`$_SESSION = []`), destrucción del archivo de sesión en el servidor con `session_destroy()` y expiración deliberada de la cookie en el cliente (`setcookie`).

---

### 2. Módulo CRUD — Hito 2 / Clase 7 (Persistencia con PDO y Sentencias Preparadas)
*Ubicación:* Carpeta [`admin/`](./admin/) y [`config/conexion.php`](./config/conexion.php)
- **Conexión Robusta con PDO (`config/conexion.php`):** Instancia de PDO con manejo de errores mediante excepciones `try-catch (\PDOException $e)`, modo de fetch asociativo y desactivación de emulación de consultas preparadas (`ATTR_EMULATE_PREPARES => false`).
- **Sentencias Preparadas Obligatorias:** Uso estricto de parámetros vinculados (`:titulo`, `:slug`, `:bajada`, `:contenido`, `:categoria_id`, etc.) en todas las operaciones, neutralizando cualquier vulnerabilidad de Inyección SQL (SQL Injection).
- **Operaciones CRUD Completas:**
  - **Create (`admin/crear.php`):** Formulario de alta e inserción de noticias con validación en servidor.
  - **Read (`admin/index.php`):** Listado tabular con información de publicaciones, miniaturas, categorías y fechas.
  - **Update (`admin/editar.php`):** Formulario precargado con datos del registro y actualización inmediata.
  - **Delete (`admin/eliminar.php`):** Baja definitiva del registro de la base de datos con confirmación y redirección.
- **Protección Anti-XSS:** Todas las salidas impresas utilizan `htmlspecialchars(..., ENT_QUOTES | ENT_HTML5, 'UTF-8')`.

---

### 3. Trabajo Práctico N° 6 — Base de Datos Relacional MySQL y Normalización
*Ubicación detallada:* [`tp6/NORMALIZACION.md`](./tp6/NORMALIZACION.md), [`tp6/schema.sql`](./tp6/schema.sql) y [`tp6/consultas.sql`](./tp6/consultas.sql)
- **Normalización de Base de Datos:**
  - **1FN (Primera Forma Normal):** Eliminación de grupos repetitivos y garantía de atomicidad en todos los campos (ej. separación de nombres, apellidos y roles).
  - **2FN (Segunda Forma Normal):** Dependencia funcional completa de las claves primarias; eliminación de redundancias parciales mediante tablas intermedias y claves foráneas.
  - **3FN (Tercera Forma Normal):** Eliminación de dependencias transitivas (ej. las categorías y estados residen en tablas normalizadas).
- **Esquema Relacional (`gyt_comunidad`):** Tablas de `usuarios`, `publicaciones`, `categorias`, `comentarios`, `secciones_contenido`, `plantel_jugadores`, `partidos_fixture`, `tabla_posiciones` y `mensajes_contacto`.
- **Integridad y Rendimiento:** Definición de claves primarias compuestas, claves foráneas (`FOREIGN KEY`) con políticas de integridad referencial, e índices para búsquedas optimizadas.

---

### 3. Clase 5 — Procesamiento de Formularios y Seguridad XSS
*Ubicación detallada:* Carpeta [`clase5/`](./clase5/)
- **Formulario POST (Contacto):** Procesamiento en el servidor de campos de texto, validación de obligatoriedad y validación de formato de correo con `filter_var($email, FILTER_VALIDATE_EMAIL)`.
- **Formulario GET (Búsqueda):** Búsqueda y filtrado de noticias en tiempo real con sanitización de términos de consulta.
- **Seguridad y Escape de Salida:** Tratamiento estricto contra inyecciones de código mediante `htmlspecialchars($campo, ENT_QUOTES | ENT_HTML5, 'UTF-8')` y `strip_tags()` antes de persistir o imprimir cualquier información.

---

### 4. Trabajo Práctico N° 4 — Migración a PHP, Modularización (SSI) y Renderizado Server-Side (SSR)
- **Migración Integral a PHP:** Transformación de la maqueta estática inicial (Hito 1 en HTML) a una arquitectura dinámica en PHP 8.
- **Modularización por Plantillas (Server-Side Includes - SSI):** Extracción de componentes comunes en la carpeta `includes/` (`header.php`, `nav.php`, `footer.php`), permitiendo una mantención centralizada.
- **Server-Side Rendering (SSR):** Inyección dinámica de títulos (`$titulo_pagina`), detección de la página en navegación para aplicar la clase `.active` en el menú (`$pagina_actual`), y renderizado en servidor de tarjetas informativas.
- **Gestión de Entorno Seguro:** Configuración desacoplada mediante variables de entorno con archivo privado `.env` y plantilla versionada `.env.example`.

---

## 📁 Estructura del Proyecto

```text
GyTSalta/
├── .env.example             # Plantilla de variables de entorno (DB host, user, pass, app config)
├── config/
│   ├── conexion.php         # Conexión PDO con bloque try-catch (Hito 2 / Clase 7)
│   └── env.php              # Lector y parseador del archivo de entorno .env
├── admin/                   # Módulo CRUD Hito 2 (Temática Noticias GyT)
│   ├── index.php            # Listado principal y lectura de registros (Read)
│   ├── crear.php            # Formulario e inserción con Prepared Statements (Create)
│   ├── editar.php           # Formulario y actualización de publicaciones (Update)
│   └── eliminar.php         # Lógica de eliminación segura con Prepared Statements (Delete)
├── auth_guard.php           # Guardián de rutas y control de acceso RBAC
├── auth.php                 # Helper de sincronización y validación de sesión
├── login.php                # Vista y controlador de inicio de sesión seguro
├── registro.php             # Formulario y alta segura de nuevos hinchas
├── logout.php               # Cierre de sesión y destrucción de cookies
├── dashboard.php            # Vista protegida: Panel de Socio / Carnet Digital (Rol: usuario/admin)
├── admin_panel.php          # Vista protegida: Panel de Control de Administración (Rol: admin)
├── index.php                # Portada del hincha + Editor CMS de noticias y roles para admin
├── historia.php             # Historia y Estadio + Editor de textos y fotos para admin
├── plantel.php              # Plantel profesional + Alta/Baja/Edición de jugadores para admin
├── partidos.php             # Fixture y Tabla + Editor de fechas y puntuaciones para admin
├── noticias.php             # Archivo de noticias + Herramientas de edición para admin
├── contacto.php             # Formulario de contacto + Bandeja privada de mensajes para admin
├── css/                     # Estilos CSS modulares, variables y responsive design
├── js/                      # JavaScript para reloj digital, modal de fotos y menú hamburguesa
├── img/                     # Recursos gráficos institucionales y escudo oficial
├── includes/                # Componentes SSI reutilizables
│   ├── header.php           # Encabezado con barra de estado de rol y sesión
│   ├── nav.php              # Navegación principal con enlaces dinámicos según rol
│   ├── footer.php           # Pie de página institucional y copyright
│   ├── db.php               # Conexión centralizada a base de datos mediante PDO
│   └── auth_guard.php       # Alias modular del guardián
├── config/
│   └── env.php              # Lector y parseador del archivo de entorno .env
├── tp6/                     # Entregables del Trabajo Práctico N° 6
│   ├── NORMALIZACION.md     # Documento formal de normalización (1FN, 2FN, 3FN)
│   ├── schema.sql           # Script DDL de creación de tablas e índices en MySQL
│   ├── consultas.sql        # Consultas DML de prueba (SELECT, INSERT, UPDATE, DELETE)
│   └── capturas/            # Capturas del proceso de normalización y base de datos
├── tp8/                     # Entregables del Trabajo Práctico N° 8
│   ├── schema_usuarios.sql  # Script DDL/DML de usuarios y credenciales iniciales
│   ├── README.md            # Documentación técnica exhaustiva de seguridad y RBAC
│   └── preparar_entrega_git_tp8.bat # Script de automatización de commits Git
└── README.md                # Esta documentación general del repositorio
```

---

## 🚀 Guía de Instalación y Puesta en Marcha Local

1. **Clonar el Repositorio de GitHub:**
   ```bash
   git clone https://github.com/fguantay39-dot/GyTSalta.git
   ```

2. **Copiar el proyecto al servidor web:**
   Ubicar la carpeta del proyecto en el directorio `htdocs` de XAMPP:
   ```text
   C:\xampp\htdocs\gyt-nuevo\
   ```

3. **Configurar el archivo de entorno `.env`:**
   Copiar `.env.example` y nombrarlo `.env`:
   ```bash
   copy .env.example .env
   ```
   Verificar las credenciales de conexión a MySQL:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=gyt_comunidad
   DB_USER=root
   DB_PASS=
   APP_ENV=local
   ```

4. **Importar la Base de Datos en MySQL:**
   - Abrir el panel de XAMPP e iniciar **Apache** y **MySQL**.
   - Ingresar a phpMyAdmin en `http://localhost/phpmyadmin/`.
   - Crear la base de datos `gyt_comunidad`.
   - Importar primero los esquemas estructurales:
     - [`tp6/schema.sql`](./tp6/schema.sql)
     - [`tp8/schema_usuarios.sql`](./tp8/schema_usuarios.sql)

5. **Acceder a la Aplicación:**
   - **Inicio / Portada Principal:** [`http://localhost/gyt-nuevo/`](http://localhost/gyt-nuevo/)
   - **Inicio de Sesión:** [`http://localhost/gyt-nuevo/login.php`](http://localhost/gyt-nuevo/login.php)
   - **Registro de Socios:** [`http://localhost/gyt-nuevo/registro.php`](http://localhost/gyt-nuevo/registro.php)

---

## 📌 Autoría y Créditos
- **Desarrollador:** Franco Guantay
- **Colegio:** EET N.º 3139 Gral. Martín Miguel de Güemes — Salta, Argentina
- **Materia:** Programación Web
- **Año:** 2026
