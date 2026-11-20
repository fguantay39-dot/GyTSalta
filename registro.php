<?php
/**
 * registro.php
 * Módulo de Registro de Nuevos Socios / Usuarios
 * Trabajo Práctico N° 8 - Seguridad, Sesiones y Control de Acceso (OWASP)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/auth_guard.php';

// Si ya está autenticado, redirigir al panel del socio
if (usuario_esta_autenticado()) {
    header('Location: dashboard.php');
    exit;
}

$errores = [];
$exito = '';
$nombre = '';
$email = '';

// Procesamiento del Formulario de Registro (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/db.php';

    // 1. Sanitización estricta de entradas (Prevención XSS)
    $nombre           = trim((string)($_POST['nombre'] ?? ''));
    $email            = trim((string)($_POST['email'] ?? ''));
    $password         = (string)($_POST['password'] ?? '');
    $confirm_password = (string)($_POST['confirm_password'] ?? '');

    // Sanitizar cadenas visibles contra inyección de HTML/JavaScript
    $nombre_sanitizado = htmlspecialchars(strip_tags($nombre), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $email_limpio      = strtolower(filter_var($email, FILTER_SANITIZE_EMAIL) ?: '');

    // 2. Validaciones de negocio
    if ($nombre === '' || mb_strlen($nombre) < 3) {
        $errores[] = 'El nombre completo debe tener al menos 3 caracteres.';
    } elseif (mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre no puede superar los 100 caracteres.';
    }

    if ($email_limpio === '' || !filter_var($email_limpio, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo electrónico válido.';
    } elseif (strlen($email_limpio) > 254) {
        $errores[] = 'El correo electrónico es demasiado extenso.';
    }

    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe contener un mínimo de 8 caracteres (Recomendación OWASP).';
    }

    if ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas ingresadas no coinciden.';
    }

    // 3. Verificación de que el correo electrónico no exista en la Base de Datos
    if (empty($errores)) {
        try {
            $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email');
            $stmtCheck->execute(['email' => $email_limpio]);
            $cuenta = (int)$stmtCheck->fetchColumn();

            if ($cuenta > 0) {
                $errores[] = 'El correo electrónico ya se encuentra registrado. Por favor, iniciá sesión.';
            } else {
                // 4. Encriptación segura con BCRYPT (OWASP)
                $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // 5. Inserción con consulta preparada (Prevención SQL Injection)
                $stmtInsert = $pdo->prepare('
                    INSERT INTO usuarios (nombre, email, password, password_hash, rol) 
                    VALUES (:nombre, :email, :password, :password_hash, :rol)
                ');

                $rol_defecto = 'usuario'; // Todo registro público es rol usuario/socio

                $stmtInsert->execute([
                    'nombre'        => $nombre_sanitizado,
                    'email'         => $email_limpio,
                    'password'      => $password_hash,
                    'password_hash' => $password_hash,
                    'rol'           => $rol_defecto,
                ]);

                // Redirigir a login con aviso de éxito
                header('Location: login.php?mensaje=registro_exitoso');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Error en registro TP8: ' . $e->getMessage());
            $errores[] = 'Ocurrió un error en el servidor al procesar el registro. Intentalo de nuevo.';
        }
    }
}

// Variables para renderizado SSR y SSI
$titulo_pagina = 'Registro de Socios | ' . $nombre_sitio;
$pagina_actual = 'registro';

require_once __DIR__ . '/includes/header.php';
?>

<main class="auth-main-container" style="background:#f4f6f9; padding: 40px 15px; min-height: calc(100vh - 200px);">
    <div style="max-width: 540px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border-top: 5px solid #0077b6;">
        
        <!-- Encabezado de la Tarjeta -->
        <div style="background: linear-gradient(135deg, #0077b6 0%, #023e8a 100%); color: #fff; padding: 25px 30px; text-align: center;">
            <div style="width: 70px; height: 70px; margin: 0 auto 12px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(0,0,0,0.2);">
                <img src="https://www.saltargentina.com.ar/wp-content/uploads/Gimnasio_y_tiro_de_salta.png" alt="Escudo Gimnasia y Tiro" style="width: 48px; height: auto;">
            </div>
            <h1 style="font-size: 1.6rem; margin: 0 0 5px; font-weight: 700;">Sumate a la Comunidad Alba</h1>
            <p style="margin: 0; font-size: 0.92rem; opacity: 0.9;">Creá tu cuenta de socio en Gimnasia y Tiro de Salta</p>
        </div>

        <div style="padding: 30px;">

            <!-- Mensajes de Error -->
            <?php if (!empty($errores)): ?>
                <div style="background: #ffebee; border-left: 4px solid #d32f2f; color: #c62828; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Se encontraron errores:</strong>
                    <ul style="margin: 6px 0 0 18px; padding: 0;">
                        <?php foreach ($errores as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulario de Registro -->
            <form action="registro.php" method="POST" novalidate style="display: flex; flex-direction: column; gap: 16px;">
                
                <div>
                    <label for="nombre" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem;">
                        Nombre completo <span style="color:#d32f2f;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Ej. Juan Pérez"
                        required 
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem;">
                        Correo electrónico <span style="color:#d32f2f;">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="socio@correo.com"
                        required 
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label for="password" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem;">
                        Contraseña <span style="color:#d32f2f;">*</span> (Mínimo 8 caracteres)
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required 
                        minlength="8"
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <div>
                    <label for="confirm_password" style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 0.9rem;">
                        Confirmar contraseña <span style="color:#d32f2f;">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Repetí tu contraseña"
                        required 
                        minlength="8"
                        style="width: 100%; padding: 11px 14px; border: 1px solid #ccc; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box;"
                    >
                </div>

                <!-- Nota de seguridad OWASP -->
                <div style="background: #e8f4fd; border-radius: 6px; padding: 10px 14px; font-size: 0.8rem; color: #023e8a; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-shield-halved" style="font-size: 1.2rem;"></i>
                    <span>Tus contraseñas se almacenan cifradas en el servidor mediante algoritmo <strong>BCRYPT</strong> con salting automático (OWASP Standard).</span>
                </div>

                <button 
                    type="submit" 
                    style="margin-top: 10px; width: 100%; padding: 12px; background: #0077b6; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#023e8a'"
                    onmouseout="this.style.background='#0077b6'"
                >
                    <i class="fa-solid fa-user-plus"></i> Registrarme en el Club
                </button>
            </form>

            <div style="margin-top: 22px; text-align: center; border-top: 1px solid #eee; padding-top: 16px; font-size: 0.9rem; color: #555;">
                ¿Ya tenés cuenta de socio? 
                <a href="login.php" style="color: #0077b6; font-weight: 700; text-decoration: none;">Iniciá sesión acá &rarr;</a>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
