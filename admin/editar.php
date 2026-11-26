<?php
/**
 * admin/editar.php
 * Edición y Actualización de Publicación (Update)
 * Requerimiento: Hito 2 / Clase 7 (Consultas Preparadas con PDO)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Obtener categorías para el desplegable
$stmtCats = $pdo->query("SELECT id, nombre FROM categorias ORDER BY id ASC");
$categorias = $stmtCats->fetchAll();

// Si se envía el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $titulo       = trim((string)($_POST['titulo'] ?? ''));
    $bajada       = trim((string)($_POST['bajada'] ?? ''));
    $contenido    = trim((string)($_POST['contenido'] ?? ''));
    $categoria_id = (int)($_POST['categoria_id'] ?? 1);
    $imagen_url   = trim((string)($_POST['imagen_url'] ?? ''));

    if ($id <= 0 || empty($titulo) || empty($contenido)) {
        $error = 'Todos los campos obligatorios deben completarse.';
    } else {
        // Consulta Preparada de Actualización (Anti SQL Injection)
        $sql = "UPDATE publicaciones 
                SET titulo = :titulo, categoria_id = :categoria_id, 
                    bajada = :bajada, contenido = :contenido, imagen_url = :imagen_url, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([
            ':id'           => $id,
            ':titulo'       => $titulo,
            ':categoria_id' => $categoria_id,
            ':bajada'       => $bajada,
            ':contenido'    => $contenido,
            ':imagen_url'   => $imagen_url,
        ]);

        if ($res) {
            header('Location: index.php?status=updated');
            exit;
        } else {
            $error = 'Error al actualizar la publicación en la base de datos.';
        }
    }
}

// Cargar datos actuales con Consulta Preparada
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmtSelect = $pdo->prepare("SELECT * FROM publicaciones WHERE id = :id");
$stmtSelect->execute([':id' => $id]);
$publicacion = $stmtSelect->fetch();

if (!$publicacion) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicación #<?= htmlspecialchars((string)$publicacion['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?> - GyT Salta</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            font-family: system-ui, -apple-system, sans-serif;
        }
        .form-container h1 {
            color: #0d233a;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
        }
        .btn-group-form {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-update {
            background: #f59e0b;
            color: white;
            padding: 0.75rem 1.8rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-update:hover { background: #d97706; }
        .btn-cancel {
            background: #94a3b8;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
        .btn-cancel:hover { background: #64748b; }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body style="background: #f0f4f8; margin: 0; padding: 1rem;">

    <div class="form-container">
        <h1>✏️ Editar Publicación #<?= htmlspecialchars((string)$publicacion['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></h1>
        <p style="color: #64748b; margin-bottom: 2rem;">Modificación de registro en MySQL mediante Prepared Statement (UPDATE)</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="editar.php?id=<?= (int)$publicacion['id'] ?>" method="POST">
            <input type="hidden" name="id" value="<?= (int)$publicacion['id'] ?>">

            <div class="form-group">
                <label for="titulo">Título de la Noticia *</label>
                <input type="text" id="titulo" name="titulo" class="form-control" value="<?= htmlspecialchars((string)$publicacion['titulo'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoría *</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= ((int)$publicacion['categoria_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string)$cat['nombre'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="bajada">Bajada o Resumen</label>
                <textarea id="bajada" name="bajada" rows="2" class="form-control"><?= htmlspecialchars((string)($publicacion['bajada'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="contenido">Cuerpo Completo de la Noticia *</label>
                <textarea id="contenido" name="contenido" rows="6" class="form-control" required><?= htmlspecialchars((string)$publicacion['contenido'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="imagen_url">URL de Imagen Ilustrativa</label>
                <input type="url" id="imagen_url" name="imagen_url" class="form-control" value="<?= htmlspecialchars((string)($publicacion['imagen_url'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">
            </div>

            <div class="btn-group-form">
                <button type="submit" class="btn-update">💾 Guardar Cambios</button>
                <a href="index.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

</body>
</html>
