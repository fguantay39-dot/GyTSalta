<?php
declare(strict_types=1);

require_once __DIR__ . '/config/env.php';

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'gyt_comunidad';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Si la base no está creada o MySQL está apagado, registrar o manejar
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
}
