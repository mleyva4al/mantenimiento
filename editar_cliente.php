<?php
require_once __DIR__ . '/config/db.php';

if (!isset($_GET['id'])) {
    header('Location: clientes.php');
    exit();
}

$id = intval($_GET['id']);


$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    echo "Cliente no encontrado.";
    exit();
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $empresa = trim($_POST['empresa']);
    $direccion = trim($_POST['direccion']);

    if ($nombre === '') {
        $error = "El nombre es obligatorio.";
    } else {
        $sql = "UPDATE clientes 
                SET nombre = ?, telefono = ?, correo = ?, empresa = ?, direccion = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nombre, $telefono, $correo, $empresa, $direccion, $id])) {
            $mensaje = "Cliente actualizado correctamente ✅";
           
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch();
        } else {
            $error = "Error al actualizar el cliente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .alerta {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-left: 5px solid #2e7d32;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: 500;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            border-left: 5px solid #b71c1c;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">
            <a href="index.php">Sistema de Mantenimiento UdeC</a>
        </div>
        <div class="nav-links">
            <a href="clientes.php">Clientes</a>
            <a href="gestion_inventario.php">Inventario</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </nav>

    <div class="container">
        <h2>Editar Cliente</h2>

        <?php if ($mensaje): ?>
            <div class="alerta"><?= htmlspecialchars($mensaje) ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
            </div>

            <div>
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" 
                       value="<?= htmlspecialchars($cliente['telefono']) ?>">
            </div>

            <div>
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" 
                       value="<?= htmlspecialchars($cliente['correo']) ?>">
            </div>

            <div>
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa" 
                       value="<?= htmlspecialchars($cliente['empresa']) ?>">
            </div>

            <div>
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" 
                       value="<?= htmlspecialchars($cliente['direccion']) ?>">
            </div>

            <button type="submit">Guardar cambios</button>
            <a href="clientes.php" 
               style="text-align:center; display:inline-block; margin-top:10px; text-decoration:none; color:#006241;">
               Cancelar
            </a>
        </form>
    </div>

    <footer>
        © 2025 - Sistema de Mantenimiento UdeC
    </footer>

</body>
</html>
