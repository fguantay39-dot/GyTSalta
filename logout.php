<?php
/**
 * logout.php
 * Módulo de Cierre Seguro de Sesión
 * Trabajo Práctico N° 8 - OWASP Session Management
 * 
 * Acciones requeridas:
 * 1. Inicialización de sesión si no estuviera activa.
 * 2. Limpieza completa de la superglobal $_SESSION (unset).
 * 3. Eliminación de las cookies de sesión del navegador (fecha de expiración en el pasado).
 * 4. Destrucción de la sesión en el servidor con session_destroy().
 * 5. Redirección a login.php?mensaje=sesion_cerrada.
 */

declare(strict_types=1);

// 1. Asegurar que la sesión esté iniciada para poder destruirla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Unset de la variable superglobal $_SESSION
$_SESSION = [];

// 3. Eliminación de las cookies de sesión del cliente (navegador)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        (bool)($params['secure'] ?? false),
        (bool)($params['httponly'] ?? true)
    );
}

// 4. Destrucción completa de la sesión en el servidor
session_destroy();

// 5. Redirección al login con mensaje confirmatorio
header('Location: login.php?mensaje=sesion_cerrada');
exit;
