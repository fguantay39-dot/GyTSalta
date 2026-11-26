# Trabajo Práctico N° 8 — Persistencia de Sesión, Hashing Seguro y Control de Acceso (RBAC)

> **Institución:** EET N.º 3139 Gral. Martín Miguel de Güemes — Salta, Argentina  
> **Materia:** Programación Web  
> **Alumno:** Franco Guantay  
> **Proyecto:** Sitio Web Oficial del Hincha — Club Atlético Gimnasia y Tiro de Salta  
> **Repositorio Oficial:** [fguantay39-dot/GyTSalta](https://github.com/fguantay39-dot/GyTSalta.git)  
> **Entorno de Ejecución:** Apache 2.4 + PHP 8.x + MySQL 8.x (XAMPP)

---

## 🎯 1. Objetivos del Trabajo Práctico N° 8

1. **Persistencia de estado en el servidor:** Implementar variables de sesión superglobales (`$_SESSION`) junto con cookies de sesión seguras (`HttpOnly`, `SameSite=Strict`, `Secure`).
2. **Módulos completos de Registro, Login y Logout:** Desarrollar el ciclo de vida de autenticación en PHP conectado de forma segura a MySQL mediante PDO con sentencias preparadas.
3. **Hashing seguro de contraseñas:** Aplicar algoritmos de derivación de claves recomendados por **OWASP** (`password_hash` con `PASSWORD_BCRYPT` y `password_verify`).
4. **Control de acceso y guardián de rutas (RBAC):** Configurar el script reutilizable `auth_guard.php` para restringir vistas públicas y proteger vistas privadas diferenciando roles (`admin` y `usuario`).
5. **Módulo de Administración Total (CMS In-Situ):** Permitir a la cuenta de Administrador oficial (`fguantay39@gmail.com`) editar todos los contenidos del sitio (portada, noticias, historia, galería, plantel, fixture y tabla) directamente desde la interfaz web, manteniendo al usuario común (`usuario`) en modo de solo lectura y navegación.

---

## 🔑 2. Credenciales de Prueba para Evaluación Docente

| Rol | Correo Electrónico | Contraseña | Destino tras Login | Capacidades en el Sitio |
|---|---|---|---|---|
| **Administrador Oficial** | `fguantay39@gmail.com` | `Admin123!` | Barra Admin + Edición In-Situ + [`admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php) | **Edición Total:** Portada, Noticias, Historia, Galería, Plantel, Fixture, Tabla y Gestión de Roles |
| **Administrador Alternativo** | `franco.guantay@gyt.com.ar` | `Admin123!` | [`admin_panel.php`](http://localhost/gyt-nuevo/admin_panel.php) | Mismos permisos y capacidades de administración completa |
| **Socio / Usuario Común** | `agustin.perez@prensaalbo.com.ar` | `Socio123!` | [`dashboard.php`](http://localhost/gyt-nuevo/dashboard.php) | **Solo Navegación:** Carnet Digital, lectura de notas y envío de consultas |
| **Nuevo Socio** | Registrarse en [`registro.php`](http://localhost/gyt-nuevo/registro.php) | `(mínimo 8 car.)` | Redirige al Login y luego al Dashboard | Asignación automática de rol `usuario` |

---

## 🏗️ 3. Arquitectura de Archivos Implementados

```text
gyt-nuevo/
├── auth_guard.php         # Guardián de rutas reutilizable y control de roles (RBAC)
├── auth.php               # Helper de sincronización y estado de sesión
├── registro.php           # Formulario y procesamiento de registro (Sanitización XSS + BCRYPT)
├── login.php              # Formulario y autenticación (session_regenerate_id + $_SESSION)
├── logout.php             # Cierre seguro (unset $_SESSION, expirar cookies, session_destroy)
├── dashboard.php          # Vista Protegida 1: Panel de Socio / Carnet Digital (Rol: usuario/admin)
├── admin_panel.php        # Vista Protegida 2: Panel de Administración Albo (Rol exclusivo: admin)
├── index.php              # Portada: Barra de estado admin, creador/editor de noticias y roles
├── historia.php           # Historia y Estadio: Edición de textos y gestión de galería fotográfica
├── plantel.php            # Plantel: Formulario de alta, edición y baja de futbolistas
├── partidos.php           # Fixture y Tabla: Creación de partidos y edición de puntos de la tabla
├── noticias.php           # Noticias: Edición y borrado de artículos
├── contacto.php           # Contacto: Formulario público + bandeja privada de lectura para admin
├── includes/
│   ├── auth_guard.php     # Alias modular del guardián
│   ├── header.php         # Encabezado SSI con badge visual de estado/rol en tiempo real
│   ├── nav.php            # Navegación SSI con enlaces condicionales según el rol activo
│   ├── db.php             # Conexión persistente mediante PDO
│   └── footer.php         # Pie de página institucional
├── config/
│   └── env.php            # Carga segura de variables de entorno (.env)
└── tp8/
    ├── schema_usuarios.sql# Script DDL/DML de la tabla usuarios en MySQL
    ├── README.md          # Esta documentación técnica
    └── preparar_entrega_git_tp8.bat # Script de commits atómicos en Git
```

---

## 🗄️ 4. Estructura de la Base de Datos (`usuarios`)

La tabla `usuarios` en la base de datos `gyt_comunidad` almacena la identidad, credenciales cifradas y roles:

```sql
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🛡️ 5. Desarrollo Técnico y Buenas Prácticas OWASP

### 5.1. Módulo de Registro (`registro.php`)
* **Sanitización estricta anti-XSS:** Todas las entradas de usuario se procesan con `strip_tags()` y `htmlspecialchars(..., ENT_QUOTES | ENT_HTML5, 'UTF-8')`. El correo se valida estrictamente con `filter_var(..., FILTER_VALIDATE_EMAIL)`.
* **Verificación de unicidad:** Se consulta a la BD mediante `SELECT COUNT(*) FROM usuarios WHERE email = :email` antes de insertar para impedir correos duplicados.
* **Cifrado de Contraseñas:** Se utiliza `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`, garantizando un salting criptográfico individual para cada registro y alta resistencia contra ataques de diccionario y fuerza bruta.
* **Consultas Preparadas con PDO:** Neutralizan cualquier vector de ataque de **Inyección SQL (SQLi)**.

### 5.2. Módulo de Autenticación (`login.php`)
* **Comparación Segura de Hash:** Se busca al usuario por email y se coteja su clave con `password_verify($password, $usuario['password'])`, previniendo ataques de análisis de tiempo (*timing attacks*).
* **Manejo Seguro de Sesión:**
  1. `session_start()` inicializa la sesión con parámetros de cookies protegidas (`HttpOnly`, `SameSite=Strict`).
  2. `session_regenerate_id(true)` se ejecuta inmediatamente después de autenticar las credenciales para mitigar el ataque de **Fijación de Sesión (Session Fixation)**.
  3. Se guardan en `$_SESSION` únicamente las variables necesarias: `usuario_id`, `usuario_nombre`, `usuario_email` y `rol`.
  4. Redirección automática según el rol asignado.

### 5.3. Guardián de Rutas y Control por Roles (`auth_guard.php`)
* `verificar_autenticacion()`: Redirige con `Location: login.php?error=acceso_denegado` si la sesión no está activa.
* `verificar_rol('admin')`: Valida si el usuario conectado cuenta con el rol `admin`. De lo contrario, impide el paso y redirige a `dashboard.php?error=rol_no_autorizado`.

### 5.4. Cierre Seguro de Sesión (`logout.php`)
* Aplica el estándar de revocación total de sesión:
  1. `$_SESSION = []`: Limpieza de la memoria del proceso.
  2. `setcookie(session_name(), '', time() - 42000, ...)`: Expiración inmediata de la cookie en el cliente.
  3. `session_destroy()`: Destrucción del archivo de sesión en el servidor.
  4. Redirección a `login.php?mensaje=sesion_cerrada`.

---

## 🎨 6. Capacidades del Sistema de Administración Total (CMS)

A solicitud del usuario, la cuenta de Administrador oficial (`fguantay39@gmail.com`) cuenta con herramientas interactivas integradas en la propia web:

1. **Barra de Rol en el Encabezado:**
   - Presente en todo el sitio, muestra en tiempo real si el usuario es Administrador o Hincha/Socio, con acceso directo al Panel y botón para cerrar sesión.
2. **Edición en Portada (`index.php`):**
   - Publicación de noticias nuevas con almacenamiento en MySQL.
   - Edición y eliminación rápida de noticias publicadas.
   - Gestor visual de roles para ascender usuarios a `admin` o cambiarlos a `usuario`.
3. **Edición en Historia y Galería (`historia.php`):**
   - Actualización de textos históricos y del Estadio Gigante del Norte.
   - Formulario para agregar imágenes a la galería y botón de borrado individual.
4. **Edición en Plantel (`plantel.php`):**
   - Alta de nuevos futbolistas, edición de goles y partidos jugados, y baja de futbolistas.
5. **Edición en Partidos y Tabla (`partidos.php`):**
   - Programación de fechas del fixture y actualización de puntuaciones en la tabla de la Primera Nacional.
6. **Buzón de Contacto (`contacto.php`):**
   - Lectura y eliminación de los mensajes enviados por los hinchas.

---

## 🧪 7. Matriz de Pruebas Realizadas

| Caso de Prueba | Entrada / Acción | Resultado Esperado | Resultado Obtenido | Estado |
|---|---|---|---|---|
| **CP-01** | Registro con correo repetido | Mensaje de error amigable | "El correo ya se encuentra registrado" | ✅ Pasó |
| **CP-02** | Registro con clave menor a 8 caracteres | Bloqueo por regla OWASP | "Mínimo de 8 caracteres" | ✅ Pasó |
| **CP-03** | Login con clave incorrecta | Error genérico sin enumeración | "Correo o contraseña incorrectos" | ✅ Pasó |
| **CP-04** | Login con cuenta Admin (`fguantay39@gmail.com`) | Regeneración de sesión y rol admin | Acceso total y herramientas de edición habilitadas | ✅ Pasó |
| **CP-05** | Login con cuenta Socio (`agustin.perez@prensaalbo.com.ar`) | Regeneración de sesión y rol usuario | Redirección a `dashboard.php` sin botones de edición | ✅ Pasó |
| **CP-06** | Acceso anónimo a `dashboard.php` | Bloqueo por `auth_guard.php` | Redirección a `login.php?error=acceso_denegado` | ✅ Pasó |
| **CP-07** | Usuario común intenta acceder a `admin_panel.php` | Bloqueo por `verificar_rol('admin')` | Redirección a `dashboard.php?error=rol_no_autorizado` | ✅ Pasó |
| **CP-08** | Clic en Logout | Destrucción de sesión y cookies | Sesión borrada y redirección a `login.php` | ✅ Pasó |
| **CP-09** | Edición de noticia / texto por Admin | Actualización en MySQL y renderizado | Contenido modificado en tiempo real | ✅ Pasó |

---

## 🚀 8. Puesta en Marcha Local
1. Iniciar **Apache** y **MySQL** en XAMPP.
2. Importar `tp8/schema_usuarios.sql` en la base de datos `gyt_comunidad` desde phpMyAdmin.
3. Ingresar al navegador en `http://localhost/gyt-nuevo/login.php`.
