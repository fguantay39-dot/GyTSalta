<?php
/**
 * admin/eliminar.php
 * Lógica de Eliminación Segura (Delete) mediante Prepared Statement PDO
 * Requerimiento: Hito 2 / Clase 7 (CRUD con MySQL)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Consulta Preparada de Eliminación (Anti SQL Injection)
        $sql = "DELETE FROM publicaciones WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header('Location: index.php?status=deleted');
        exit;
    } catch (\PDOException $e) {
        die("Error al eliminar el registro: " . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

header('Location: index.php');
exit;
