<?php
require_once 'config/conexion.php';

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
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Ingresa un correo valido.";
    if ($ciudad === '') $errores[] = "Ciudad es obligatoria.";
    if ($categoria_id <= 0) $errores[] = "Selecciona una categoria valida.";

    if (empty($errores)) {
        $stmt = $pdo->prepare("INSERT INTO contactos (nombre, telefono, correo, ciudad, categoria_id, favorito) 
                               VALUES (:nombre, :telefono, :correo, :ciudad, :categoria_id, :favorito)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':ciudad' => $ciudad,
            ':categoria_id' => $categoria_id,
            ':favorito' => $favorito
        ]);
        header("Location: index.php?msg=creado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">Registrar Nuevo Contacto</h2>
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
                    <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" required value="<?= htmlspecialchars($_POST['ciudad'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($_POST['categoria_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="favorito" id="favorito" <?= isset($_POST['favorito']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="favorito">Marcar como contacto favorito</label>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Contacto</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>