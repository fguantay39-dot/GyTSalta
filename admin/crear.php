<?php
/**
 * admin/crear.php
 * Inserción de Datos Acomodada al Proyecto GyT de Salta (Create)
 * Requerimiento: Hito 2 / Clase 7 (Consultas Preparadas con PDO)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

// Obtener categorías dinámicas de la base de datos
$stmtCats = $pdo->query("SELECT id, nombre FROM categorias ORDER BY id ASC");
$categorias = $stmtCats->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura y sanitización preliminar
    $titulo       = trim((string)($_POST['titulo'] ?? ''));
    $bajada       = trim((string)($_POST['bajada'] ?? ''));
    $contenido    = trim((string)($_POST['contenido'] ?? ''));
    $categoria_id = (int)($_POST['categoria_id'] ?? 1);
    $imagen_url   = trim((string)($_POST['imagen_url'] ?? ''));

    if (empty($titulo) || empty($contenido)) {
        $error = 'El título y el contenido son obligatorios.';
    } else {
        // Generar slug
        $slugBase = mb_strtolower(trim($titulo), 'UTF-8');
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slugBase);
        $slug = preg_replace('/-+/', '-', (string)$slug) . '-' . time();

        $usuario_id = !empty($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 5; // 5 = fguantay39 (Admin)
        $estado_id  = 2; // 2 = Publicado

        if (empty($imagen_url)) {
            $imagen_url = 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80';
        }

        // Consulta Preparada Obligatoria (Anti SQL Injection)
        $sql = "INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, slug, bajada, contenido, imagen_url) 
                VALUES (:usuario_id, :categoria_id, :estado_id, :titulo, :slug, :bajada, :contenido, :imagen_url)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':usuario_id'   => $usuario_id,
            ':categoria_id' => $categoria_id,
            ':estado_id'    => $estado_id,
            ':titulo'       => $titulo,
            ':slug'         => $slug,
            ':bajada'       => $bajada,
            ':contenido'    => $contenido,
            ':imagen_url'   => $imagen_url,
        ]);

        if ($resultado) {
            header('Location: index.php?status=created');
            exit;
        } else {
            $error = 'Error al insertar el registro en la base de datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Publicación - Administración GyT</title>
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
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #0080ff;
            box-shadow: 0 0 0 3px rgba(0,128,255,0.15);
        }
        .btn-group-form {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-submit {
            background: #0080ff;
            color: white;
            padding: 0.75rem 1.8rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { background: #0066cc; }
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
        <h1>➕ Agregar Nueva Noticia / Publicación</h1>
        <p style="color: #64748b; margin-bottom: 2rem;">Inserción de registro en MySQL mediante Consulta Preparada (Prepared Statement PDO)</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="crear.php" method="POST">
            <div class="form-group">
                <label for="titulo">Título de la Noticia *</label>
                <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Ej.: Gimnasia y Tiro se prepara para el clásico norteño" required>
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoría *</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars((string)$cat['nombre'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="bajada">Bajada o Resumen</label>
                <textarea id="bajada" name="bajada" rows="2" class="form-control" placeholder="Breve síntesis introductoria de la noticia..."></textarea>
            </div>

            <div class="form-group">
                <label for="contenido">Cuerpo Completo de la Noticia *</label>
                <textarea id="contenido" name="contenido" rows="6" class="form-control" placeholder="Redactá todo el desarrollo de la noticia aquí..." required></textarea>
            </div>

            <div class="form-group">
                <label for="imagen_url">URL de Imagen Ilustrativa (Opcional)</label>
                <input type="url" id="imagen_url" name="imagen_url" class="form-control" placeholder="https://ejemplo.com/foto-noticia.jpg">
            </div>

            <div class="btn-group-form">
                <button type="submit" class="btn-submit">💾 Guardar Noticia</button>
                <a href="index.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

</body>
</html>
