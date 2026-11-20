<?php
/**
 * auth_guard.php
 * Guardián de Rutas y Control de Acceso por Roles (TP8 - Programación Web)
 * 
 * Funcionalidad:
 * 1. Configura parámetros de cookies seguras (HttpOnly, SameSite, Secure en HTTPS).
 * 2. Inicia la sesión si aún no fue iniciada.
 * 3. Proporciona funciones reutilizables:
 *    - verificar_autenticacion(): Redirige a login.php si no hay sesión activa.
 *    - verificar_rol(...$roles): Verifica si el usuario posee los roles permitidos.
 */

declare(strict_types=1);

// Configuración de cookies seguras de sesión (Recomendaciones OWASP)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, // Cookie de sesión (se destruye al cerrar el navegador)
        'path'     => '/',
        'domain'   => '',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, // Previene lectura de cookies mediante scripts maliciosos (XSS)
        'samesite' => 'Strict', // Mitiga ataques de falsificación de petición en sitios cruzados (CSRF)
    ]);
    session_start();
}

/**
 * Verifica si el usuario actual posee una sesión válida iniciada.
 * Si no está autenticado, redirige a login.php con código de error.
 */
function verificar_autenticacion(): void
{
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        header('Location: login.php?error=acceso_denegado');
        exit;
    }
}

/**
 * Verifica si el usuario autenticado posee uno de los roles autorizados.
 * Si no cumple con el rol requerido, redirige a una vista segura con aviso.
 * 
 * @param string ...$roles_permitidos Lista de roles válidos (ej: 'admin', 'usuario')
 */
function verificar_rol(string ...$roles_permitidos): void
{
    verificar_autenticacion();

    $rol_actual = strtolower(trim((string)($_SESSION['rol'] ?? 'usuario')));
    $roles_permitidos_normalizados = array_map(static fn($r) => strtolower(trim($r)), $roles_permitidos);

    if (!in_array($rol_actual, $roles_permitidos_normalizados, true)) {
        header('Location: dashboard.php?error=rol_no_autorizado');
        exit;
    }
}

/**
 * Retorna true si el usuario actual tiene una sesión activa.
 */
function usuario_esta_autenticado(): bool
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Retorna true si el usuario autenticado tiene rol de administrador.
 */
function es_admin(): bool
{
    return usuario_esta_autenticado() && strtolower((string)($_SESSION['rol'] ?? '')) === 'admin';
}

// Ejecución directa si se incluye como script guardián
if (isset($rol_requerido)) {
    verificar_rol($rol_requerido);
}
