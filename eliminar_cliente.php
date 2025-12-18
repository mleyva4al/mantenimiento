<?php
require_once __DIR__ . '/config/db.php';

if (!isset($_GET['id'])) {
    die("ID de cliente no proporcionado");
}

$id = intval($_GET['id']);


$stmt = $pdo->prepare("DELETE FROM clientes WHERE id=?");
$stmt->execute([$id]);

echo "<script>
        alert('Cliente eliminado correctamente');
        window.location.href='clientes.php';
      </script>";
exit;
?>
