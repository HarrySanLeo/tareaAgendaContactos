<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id");
$stmt->execute([':id' => $id]);
$contacto = $stmt->fetch();

if (!$contacto) {
    header("Location: index.php");
    exit;
}

$categorias = $pdo->query("SELECT id, nombre FROM categorias ORDER BY id ASC")->fetchAll();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $favorito = isset($_POST['favorito']) ? 1 : 0;

    if ($nombre === '') $errores[] = "El nombre del contacto es obligatorio.";
    if ($telefono === '') $errores[] = "El numero de telefono es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Ingresa un coorreo valido";
    if ($ciudad === '') $errores[] = "Ciudad es obligatoria.";
    if ($categoria_id <= 0) $errores[] = "Selecciona una categoria valida.";

    if (empty($errores)) {
        $update = $pdo->prepare("UPDATE contactos 
                                 SET nombre = :nombre, telefono = :telefono, correo = :correo, 
                                     ciudad = :ciudad, categoria_id = :categoria_id, favorito = :favorito 
                                 WHERE id = :id");
        $update->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':ciudad' => $ciudad,
            ':categoria_id' => $categoria_id,
            ':favorito' => $favorito,
            ':id' => $id
        ]);
        header("Location: index.php?msg=actualizado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h2 class="h5 mb-0">Editar Contacto</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errores as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? $contacto['nombre']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required value="<?= htmlspecialchars($_POST['telefono'] ?? $contacto['telefono']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" required value="<?= htmlspecialchars($_POST['correo'] ?? $contacto['correo']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" required value="<?= htmlspecialchars($_POST['ciudad'] ?? $contacto['ciudad']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select" required>
                        <?php
                        $catActual = $_POST['categoria_id'] ?? $contacto['categoria_id'];
                        foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($catActual == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check mb-4">
                    <?php $favActual = $_SERVER['REQUEST_METHOD'] === 'POST' ? isset($_POST['favorito']) : (bool)$contacto['favorito']; ?>
                    <input class="form-check-input" type="checkbox" name="favorito" id="favorito" <?= $favActual ? 'checked' : '' ?>>
                    <label class="form-check-label" for="favorito">Marcar como contacto favorito</label>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-warning">Actualizar Contacto</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>