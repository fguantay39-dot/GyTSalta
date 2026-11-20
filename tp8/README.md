# Trabajo Práctico N° 8 — Persistencia de Sesión, Hashing Seguro y Control de Acceso (RBAC)

> **Institución:** EET N.º 3139 Gral. Martín Miguel de Güemes — Salta  
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

---

## 🏗️ 2. Arquitectura de Archivos Implementados

```text
gyt-nuevo/
├── auth_guard.php         # Guardián de rutas reutilizable y control de roles (RBAC)
├── registro.php           # Formulario y procesamiento de registro (Sanitización XSS + BCRYPT)
├── login.php              # Formulario y autenticación (session_regenerate_id + $_SESSION)
├── logout.php             # Cierre seguro (unset $_SESSION, expirar cookies, session_destroy)
├── dashboard.php          # Vista Protegida 1: Panel de Socio / Carnet Digital (Rol: usuario/admin)
├── admin_panel.php        # Vista Protegida 2: Panel de Administración Albo (Rol exclusivo: admin)
├── includes/
│   ├── auth_guard.php     # Alias del guardián de rutas
│   ├── header.php         # Encabezado SSI actualizado con badge de usuario autenticado
│   └── nav.php            # Navegación lateral SSI con enlaces dinámicos según sesión
├── config/
│   └── env.php            # Carga de credenciales y variables de entorno
├── tp8/
│   ├── schema_usuarios.sql# Script DDL/DML de la tabla usuarios en MySQL
│   ├── README.md          # Esta documentación oficial
│   └── preparar_entrega_git_tp8.bat # Script de commits y sincronización Git
└── README.md              # Documentación principal del repositorio
```

---

## 🗄️ 3. Estructura de la Base de Datos (`usuarios`)

La tabla `usuarios` en MySQL (`gyt_comunidad`) cumple estrictamente la especificación requerida:

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

## 🛡️ 4. Desarrollo Técnico y Buenas Prácticas OWASP

### 4.1. Módulo de Registro (`registro.php`)
* **Sanitización estricta:** Todas las entradas de usuario se procesan con `strip_tags()` y `htmlspecialchars(..., ENT_QUOTES | ENT_HTML5, 'UTF-8')` para prevenir ataques de **Cross-Site Scripting (XSS)**. El correo se valida con `filter_var(..., FILTER_VALIDATE_EMAIL)`.
* **Verificación de unicidad:** Se consulta a la BD mediante `SELECT COUNT(*) FROM usuarios WHERE email = :email` para garantizar que no existan correos duplicados.
* **Cifrado de Contraseñas:** Se utiliza `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`, asegurando salting criptográfico por cada usuario y resistencia a ataques de fuerza bruta.
* **Consultas Preparadas:** Se usa PDO con parámetros vinculados (`:nombre`, `:email`, `:password`, `:rol`) para neutralizar cualquier intento de **SQL Injection**.

### 4.2. Módulo de Autenticación (`login.php`)
* **Comparación Segura de Hash:** Se busca al usuario por correo y se evalúa su clave mediante `password_verify($password, $usuario['password'])`, previniendo ataques de temporización (timing attacks).
* **Manejo Seguro de Sesión:**
  1. `session_start()` con parámetros de cookies protegidas.
  2. `session_regenerate_id(true)` ejecutado de inmediato tras el login para mitigar el ataque de **Fijación de Sesión (Session Fixation)**.
  3. Almacenamiento exclusivo de datos indispensables en `$_SESSION`: `$_SESSION['usuario_id']`, `$_SESSION['usuario_nombre']`, `$_SESSION['rol']` y `$_SESSION['usuario_email']`.
  4. Redirección automática según rol: `admin` &rarr; `admin_panel.php`, `usuario` &rarr; `dashboard.php`.

### 4.3. Guardián de Rutas y Control por Roles (`auth_guard.php`)
Script modular que ofrece dos niveles de seguridad:
* `verificar_autenticacion()`: Redirige con `Location: login.php?error=acceso_denegado` si `$_SESSION['usuario_id']` no está definido.
* `verificar_rol('admin')`: Valida si el usuario conectado posee el rol permitido. En caso negativo, bloquea el acceso y redirige a `dashboard.php?error=rol_no_autorizado`.

### 4.4. Cierre Seguro de Sesión (`logout.php`)
Aplica el estándar de revocación total de sesión:
1. `$_SESSION = []`: Vaciado de variables en memoria.
2. `setcookie(session_name(), '', time() - 42000, ...)`: Expiración de la cookie de sesión en el navegador cliente.
3. `session_destroy()`: Eliminación del archivo de sesión en el servidor (`/tmp` o `xampp/tmp`).
4. Redirección a `login.php?mensaje=sesion_cerrada`.

---

## 🔑 5. Credenciales de Prueba para Evaluación

| Rol | Correo Electrónico | Contraseña | Destino tras Login |
|---|---|---|---|
| **Administrador** | `franco.guantay@gyt.com.ar` | `Admin123!` | `admin_panel.php` |
| **Socio / Usuario** | `agustin.perez@prensaalbo.com.ar` | `Socio123!` | `dashboard.php` |
| **Nuevo Socio** | Registro en `registro.php` | `(mínimo 8 car.)` | `login.php` &rarr; `dashboard.php` |

---

## 🧪 6. Matriz de Pruebas Realizadas

| Caso de Prueba | Entrada / Acción | Resultado Esperado | Resultado Obtenido | Estado |
|---|---|---|---|---|
| **CP-01** | Registro con correo repetido | Mensaje de error amigable | "El correo ya se encuentra registrado" | ✅ Pasó |
| **CP-02** | Registro con clave menor a 8 caracteres | Bloqueo por regla OWASP | "Mínimo de 8 caracteres" | ✅ Pasó |
| **CP-03** | Login con clave incorrecta | Error genérico sin enumeración | "Correo o contraseña incorrectos" | ✅ Pasó |
| **CP-04** | Login correcto de Administrador | session_regenerate_id + redirect | Redirección a `admin_panel.php` | ✅ Pasó |
| **CP-05** | Login correcto de Usuario común | session_regenerate_id + redirect | Redirección a `dashboard.php` | ✅ Pasó |
| **CP-06** | Acceso anónimo a `dashboard.php` | Detección por `auth_guard.php` | Redirección a `login.php?error=acceso_denegado` | ✅ Pasó |
| **CP-07** | Usuario común intenta acceder a `admin_panel.php` | Detección por `verificar_rol('admin')` | Redirección a `dashboard.php?error=rol_no_autorizado` | ✅ Pasó |
| **CP-08** | Clic en Logout | Destrucción de sesión y cookies | Sesión borrada y redirect a `login.php` | ✅ Pasó |

---

## 🚀 7. Instrucciones de Despliegue en XAMPP

1. Iniciar **Apache** y **MySQL** desde el Panel de Control de XAMPP.
2. Abrir phpMyAdmin en `http://localhost/phpmyadmin/`.
3. Importar el script `tp8/schema_usuarios.sql` en la base de datos `gyt_comunidad`.
4. Ingresar al navegador en: `http://localhost/gyt-nuevo/login.php`.
