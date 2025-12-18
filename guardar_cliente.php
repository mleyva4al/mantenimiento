<?php
require_once __DIR__ . '/config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre_completo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $empresa  = trim($_POST['empresa'] ?? '');
    $direccion= trim($_POST['direccion'] ?? '');

    if (empty($nombre)) {
        echo "<script>alert('El nombre del cliente es obligatorio.'); window.history.back();</script>";
        exit;
    }

    try {
        $sql = "INSERT INTO clientes (nombre, telefono, correo, direccion, empresa, fecha_registro)
                VALUES (:nombre, :telefono, :correo, :direccion, :empresa, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':direccion' => $direccion,
            ':empresa' => $empresa
        ]);

        echo "<script>
                alert('✅ Cliente agregado correctamente.');
                window.location.href='clientes.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('❌ Error al guardar el cliente: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: clientes.php");
    exit;
}
?>
