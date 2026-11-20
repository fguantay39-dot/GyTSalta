<?php
/**
 * login.php
 * Módulo de Autenticación e Inicio de Sesión
 * Trabajo Práctico N° 8 - Persistencia de Sesión, Hashing y OWASP
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/auth_guard.php';

// Si ya hay una sesión activa, redirigir según el rol
if (usuario_esta_autenticado()) {
    if (es_admin()) {
        header('Location: admin_panel.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$errores = [];
$email = '';
$mensaje_info = '';

// Capturar avisos provenientes de redirecciones (Guardián, Registro o Logout)
$codigo_mensaje = $_GET['mensaje'] ?? '';
$codigo_error   = $_GET['error'] ?? '';

if ($codigo_mensaje === 'registro_exitoso') {
    $mensaje_info = '¡Cuenta creada con éxito! Ahora podés ingresar con tu correo y contraseña.';
} elseif ($codigo_mensaje === 'sesion_cerrada') {
    $mensaje_info = 'Has cerrado sesión correctamente. ¡Hasta la próxima, Albo!';
}

if ($codigo_error === 'acceso_denegado') {
    $errores[] = 'Acceso denegado: Debés iniciar sesión para ingresar a esa sección.';
} elseif ($codigo_error === 'rol_no_autorizado') {
    $errores[] = 'Permisos insuficientes: No contás con el rol requerido para ver esa página.';
}

// Procesamiento del Inicio de Sesión (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/db.php';

    // 1. Sanitización de entradas
    $email    = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    // 2. Validación de campos obligatorios
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo electrónico válido.';
    }

    if ($password === '') {
        $errores[] = 'Ingresá tu contraseña.';
    }

    // 3. Verificación de credenciales en la base de datos
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare('
                SELECT id, nombre, email, password, password_hash, rol 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1
            ');
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch();

            // Hash a verificar (soporta columna 'password' o 'password_hash')
            $hashAlmacenado = $usuario['password'] ?? ($usuario['password_hash'] ?? '');

            // 4. Verificación segura con password_verify (OWASP)
            if ($usuario && !empty($hashAlmacenado) && password_verify($password, $hashAlmacenado)) {
                
                // PASO 1: Invocación segura de session_start() (manejada con cookies seguras en auth_guard.php)
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // PASO 2: Regenerar el ID de sesión para mitigar ataques de fijación de sesión (Session Fixation)
                session_regenerate_id(true);

                // PASO 3: Almacenar en la superglobal $_SESSION los datos claves del usuario
                $_SESSION['usuario_id']     = (int)$usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['rol']            = strtolower((string)($usuario['rol'] ?? 'usuario'));
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['ultimo_acceso']  = time();

                // PASO 4: Redirección al panel protegido o dashboard según su rol
                if ($_SESSION['rol'] === 'admin') {
                    header('Location: admin_panel.php?login=ok');
                } else {
                    header('Location: dashboard.php?login=ok');
                }
                exit;

            } else {
                // Mensaje genérico para prevenir enumeración de usuarios (Buenas Prácticas OWASP)
                $errores[] = 'Correo electrónico o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            error_log('Error en login TP8: ' . $e->getMessage());
            $errores[] = 'Error al conectar con la base de datos. Revisá que MySQL en XAMPP esté activo.';
        }
    }
}

// Variables dinámicas para SSR y SSI
$titulo_pagina = 'Iniciar Sesión | ' . $nombre_sitio;
$pagina_actual = 'login';

require_once __DIR__ . '/includes/header.php';
?>

<main class="auth-main-container" style="background:#f4f6f9; padding: 40px 15px; min-height: calc(100vh - 200px);">
    <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border-top: 5px solid #0077b6;">
        
        <!-- Encabezado de la Tarjeta -->
        <div style="background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%); color: #fff; padding: 25px 30px; text-align: center;">
            <div style="width: 70px; height: 70px; margin: 0 auto 12px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(0,0,0,0.2);">
                <img src="https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png" alt="Escudo Gimnasia y Tiro" style="width: 48px; height: auto;">
            </div>
            <h1 style="font-size: 1.6rem; margin: 0 0 5px; font-weight: 700;">Ingreso a la Comunidad</h1>
            <p style="margin: 0; font-size: 0.92rem; opacity: 0.9;">Accedé a tu cuenta del Club Atlético Gimnasia y Tiro</p>
        </div>

        <div style="padding: 30px;">

            <!-- Mensaje de Información / Éxito -->
            <?php if (!empty($mensaje_info)): ?>
                <div style="background: #e8f5e9; border-left: 4px solid #2e7d32; color: #1b5e20; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_info, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Mensajes de Error -->
            <?php if (!empty($errores)): ?>
                <div style="background: #ffebee; border-left: 4px solid #d32f2f; color: #c62828; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Atención:</strong>
                    <ul style="margin: 6px 0 0 18px; padding: 0;">
                        <?php foreach ($errores as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulario de Login -->
            <form action="login.php" method="POST" novalidate style="display: flex; flex-direction: column; gap: 18px;">
                
                <div>
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem;">
                        Correo electrónico
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="tu-correo@ejemplo.com"
                        required 
                        autocomplete="email"
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label for="password" style="font-weight: 600; color: #333; font-size: 0.9rem;">
                            Contraseña
                        </label>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required 
                        autocomplete="current-password"
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <button 
                    type="submit" 
                    style="margin-top: 6px; width: 100%; padding: 12px; background: #0077b6; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#023e8a'"
                    onmouseout="this.style.background='#0077b6'"
                >
                    <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                </button>
            </form>

            <!-- Credenciales de prueba para el evaluador / docente -->
            <div style="margin-top: 24px; background: #f8f9fa; border: 1px dashed #0077b6; border-radius: 8px; padding: 12px 14px; font-size: 0.82rem; color: #444;">
                <strong style="color: #0077b6;"><i class="fa-solid fa-key"></i> Credenciales de Prueba (TP8):</strong>
                <ul style="margin: 6px 0 0 16px; padding: 0;">
                    <li><strong>Admin:</strong> <code>franco.guantay@gyt.com.ar</code> | Clave: <code>Admin123!</code></li>
                    <li><strong>Socio:</strong> <code>agustin.perez@prensaalbo.com.ar</code> | Clave: <code>Socio123!</code></li>
                </ul>
            </div>

            <div style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 16px; font-size: 0.9rem; color: #555;">
                ¿Aún no tenés cuenta? 
                <a href="registro.php" style="color: #0077b6; font-weight: 700; text-decoration: none;">Registrate como socio acá &rarr;</a>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
