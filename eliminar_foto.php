<?php
session_start();
require 'includes/auth_check.php'; 
require 'config/db.php';         

$id_foto = $_GET['id_foto'] ?? null;
$id_dictamen = $_GET['id_dictamen'] ?? null; 


if (!filter_var($id_foto, FILTER_VALIDATE_INT) || !filter_var($id_dictamen, FILTER_VALIDATE_INT)) {
    die("Error: IDs no válidos.");
}

try {
    
    $stmt = $pdo->prepare("SELECT * FROM dictamen_fotos WHERE id = ? AND id_dictamen = ?");
    $stmt->execute([$id_foto, $id_dictamen]);
    $foto = $stmt->fetch();

    if (!$foto) {
        die("Error: Foto no encontrada.");
    }

    if (file_exists($foto['ruta_archivo'])) {
        unlink($foto['ruta_archivo']); 
    }


    $sql = "DELETE FROM dictamen_fotos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_foto]);
    header("Location: editar_dictamen.php?id=" . $id_dictamen . "&foto_exito=1");
    exit;

} catch (PDOException $e) {
    // Redirigir con un mensaje de error
    header("Location: editar_dictamen.php?id=" . $id_dictamen . "&foto_error=1");
    exit;
}
?>