<?php
require_once 'config/conexion.php';

// Capturar filtros de busqueda, ordenamiento y favoritos
$buscar = trim($_GET['buscar'] ?? '');
$campo = $_GET['campo'] ?? 'todos';
$orden = $_GET['orden'] ?? 'fecha_desc';
$solo_favoritos = isset($_GET['favoritos']) && $_GET['favoritos'] === '1';

$condiciones = [];
$parametros = [];

// A: Buscar por nombre, correo o ciudad
if ($buscar !== '') {
    if ($campo === 'nombre') {
        $condiciones[] = "c.nombre LIKE :buscar";
        $parametros[':buscar'] = "%$buscar%";
    } elseif ($campo === 'correo') {
        $condiciones[] = "c.correo LIKE :buscar";
        $parametros[':buscar'] = "%$buscar%";
    } elseif ($campo === 'ciudad') {
        $condiciones[] = "c.ciudad LIKE :buscar";
        $parametros[':buscar'] = "%$buscar%";
    } else {
        $condiciones[] = "(c.nombre LIKE :buscar OR c.correo LIKE :buscar OR c.ciudad LIKE :buscar)";
        $parametros[':buscar'] = "%$buscar%";
    }
}

// Opcion D: Filtrar solo favoritos
if ($solo_favoritos) {
    $condiciones[] = "c.favorito = 1";
}

$sql = "SELECT c.*, cat.nombre AS categoria_nombre 
        FROM contactos c 
        INNER JOIN categorias cat ON c.categoria_id = cat.id";

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

// Opción B: Ordenamiento por nombre, ciudad o fecha de creacion
switch ($orden) {
    case 'nombre_asc':
        $sql .= " ORDER BY c.nombre ASC";
        break;
    case 'nombre_desc':
        $sql .= " ORDER BY c.nombre DESC";
        break;
    case 'ciudad_asc':
        $sql .= " ORDER BY c.ciudad ASC, c.nombre ASC";
        break;
    case 'fecha_asc':
        $sql .= " ORDER BY c.fecha_creacion ASC";
        break;
    case 'fecha_desc':
    default:
        $sql .= " ORDER BY c.fecha_creacion DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$contactos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Personal de Contactos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Agenda Personal de Contactos</h1>
        <a href="crear.php" class="btn btn-primary">+ Nuevo Contacto</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            if ($_GET['msg'] === 'creado') echo "Contacto registrado correctamente.";
            elseif ($_GET['msg'] === 'actualizado') echo "Contacto actualizado correctamente.";
            elseif ($_GET['msg'] === 'eliminado') echo "Contacto eliminado correctamente.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Panel de Búsqueda, Ordenamiento y Filtro de Favoritos -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Buscar contacto</label>
                    <input type="text" name="buscar" class="form-control" placeholder="Escribe nombre, correo o ciudad..." value="<?= htmlspecialchars($buscar) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Buscar por</label>
                    <select name="campo" class="form-select">
                        <option value="todos" <?= $campo === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="nombre" <?= $campo === 'nombre' ? 'selected' : '' ?>>Nombre</option>
                        <option value="correo" <?= $campo === 'correo' ? 'selected' : '' ?>>Correo</option>
                        <option value="ciudad" <?= $campo === 'ciudad' ? 'selected' : '' ?>>Ciudad</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Ordenar por</label>
                    <select name="orden" class="form-select">
                        <option value="fecha_desc" <?= $orden === 'fecha_desc' ? 'selected' : '' ?>>Fecha creación (Más recientes)</option>
                        <option value="fecha_asc" <?= $orden === 'fecha_asc' ? 'selected' : '' ?>>Fecha creación (Más antiguos)</option>
                        <option value="nombre_asc" <?= $orden === 'nombre_asc' ? 'selected' : '' ?>>Nombre (A - Z)</option>
                        <option value="nombre_desc" <?= $orden === 'nombre_desc' ? 'selected' : '' ?>>Nombre (Z - A)</option>
                        <option value="ciudad_asc" <?= $orden === 'ciudad_asc' ? 'selected' : '' ?>>Ciudad (A - Z)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="favoritos" value="1" id="checkFav" <?= $solo_favoritos ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="checkFav">Solo favoritos</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Aplicar</button>
                        <a href="index.php" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Contactos -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Fav</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Categoría</th>
                        <th>Fecha Creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contactos)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No se encontraron contactos con los filtros seleccionados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contactos as $c): ?>
                            <tr>
                                <td class="text-center">
                                    <a href="favorito.php?id=<?= $c['id'] ?>" class="text-decoration-none fs-5" title="Cambiar favorito">
                                        <?= $c['favorito'] ? '⭐' : '☆' ?>
                                    </a>
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['telefono']) ?></td>
                                <td><?= htmlspecialchars($c['correo']) ?></td>
                                <td><?= htmlspecialchars($c['ciudad']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($c['categoria_nombre']) ?></span></td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($c['fecha_creacion'])) ?></td>
                                <td class="text-end">
                                    <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="eliminar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este contacto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>