<?php
/**
 * admin/index.php
 * Listado Principal (Read) del Módulo CRUD - Publicaciones / Noticias
 * Requerimiento: Hito 2 / Clase 7 (CRUD con PDO y MySQL)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

// Si existe sesión iniciada, permitir verificar; si no, permitir uso administrativo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Consulta segura con PDO
$sql = "
    SELECT p.id, p.titulo, p.bajada, p.imagen_url, p.created_at, 
           COALESCE(c.nombre, 'General') AS categoria
    FROM publicaciones p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    ORDER BY p.id DESC
";
$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll();

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contenido - Club Atlético Gimnasia y Tiro de Salta</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-crud-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            font-family: system-ui, -apple-system, sans-serif;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .admin-header h1 {
            color: #0d233a;
            font-size: 1.8rem;
            margin: 0;
        }
        .btn-crud {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary-crud { background: #0080ff; color: white; }
        .btn-primary-crud:hover { background: #0066cc; }
        .btn-warning-crud { background: #f59e0b; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem; }
        .btn-warning-crud:hover { background: #d97706; }
        .btn-danger-crud { background: #ef4444; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem; }
        .btn-danger-crud:hover { background: #dc2626; }
        .btn-secondary-crud { background: #64748b; color: white; }
        .btn-secondary-crud:hover { background: #475569; }

        .alert-crud {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

        .table-responsive {
            overflow-x: auto;
        }
        .crud-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }
        .crud-table th, .crud-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .crud-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .crud-table tr:hover {
            background: #f1f5f9;
        }
        .badge-cat {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }
        .actions-cell {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .thumb-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>
<body style="background: #f0f4f8; margin: 0; padding: 1rem;">

    <div class="admin-crud-container">
        <div class="admin-header">
            <div>
                <h1>⚽ CRUD de Noticias y Publicaciones — GyT de Salta</h1>
                <p style="color: #64748b; margin: 0.3rem 0 0 0;">Módulo de Administración de Contenidos (Clase 6 / 7 - Persistencia con PDO y Sentencias Preparadas)</p>
            </div>
            <div style="display: flex; gap: 0.8rem;">
                <a href="../index.php" class="btn-crud btn-secondary-crud">🏠 Volver al Sitio</a>
                <a href="crear.php" class="btn-crud btn-primary-crud">➕ Agregar Nueva Noticia</a>
            </div>
        </div>

        <?php if ($status === 'created'): ?>
            <div class="alert-crud alert-success">✅ ¡Noticia creada e insertada exitosamente en la base de datos MySQL!</div>
        <?php elseif ($status === 'updated'): ?>
            <div class="alert-crud alert-info">✏️ ¡Noticia actualizada correctamente mediante sentencia preparada!</div>
        <?php elseif ($status === 'deleted'): ?>
            <div class="alert-crud alert-success">🗑️ ¡Noticia eliminada de la base de datos de forma segura!</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 70px;">Foto</th>
                        <th>Título</th>
                        <th>Bajada / Descripción</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th style="width: 170px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registros)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                                No hay noticias registradas. Hacé clic en "Agregar Nueva Noticia" para crear la primera.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registros as $item): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars((string)$item['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></strong></td>
                                <td>
                                    <?php if (!empty($item['imagen_url'])): ?>
                                        <img src="<?= htmlspecialchars((string)$item['imagen_url'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>" alt="Miniatura" class="thumb-img" onerror="this.src='https://placehold.co/50x50?text=GyT'">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #64748b;">Sin foto</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars((string)$item['titulo'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></strong>
                                </td>
                                <td style="max-width: 320px; color: #475569; font-size: 0.85rem;">
                                    <?= htmlspecialchars(mb_substr((string)($item['bajada'] ?? ''), 0, 100, 'UTF-8') . (mb_strlen((string)($item['bajada'] ?? '')) > 100 ? '...' : ''), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="badge-cat"><?= htmlspecialchars((string)$item['categoria'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></span>
                                </td>
                                <td style="font-size: 0.8rem; color: #64748b;">
                                    <?= htmlspecialchars((string)substr((string)$item['created_at'], 0, 10), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="editar.php?id=<?= urlencode((string)$item['id']) ?>" class="btn-crud btn-warning-crud">✏️ Editar</a>
                                        <a href="eliminar.php?id=<?= urlencode((string)$item['id']) ?>" class="btn-crud btn-danger-crud" onclick="return confirm('¿Confirma eliminar esta publicación (#<?= htmlspecialchars((string)$item['id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>) de forma permanente?')">🗑️ Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
