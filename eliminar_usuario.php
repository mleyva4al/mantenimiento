<?php
// eliminar_usuario.php

require 'config/db.php';
session_start();
require 'includes/admin_check.php'; // ¡Súper protegido!

$id_a_eliminar = $_GET['id'] ?? null;

// Validar ID
if (!filter_var($id_a_eliminar, FILTER_VALIDATE_INT)) {
    die("Error: ID no válido.");
}

// === ¡VERIFICACIÓN DE SEGURIDAD CRÍTICA! ===
if ($id_a_eliminar == $_SESSION['user_id']) {
    die("Error: No puedes eliminarte a ti mismo. Pídele a otro administrador que lo haga. <a href='gestion_usuarios.php'>Volver</a>");
}
// === FIN DE LA VERIFICACIÓN ===

try {
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_a_eliminar]);

    // Redirigir de vuelta al panel de admin
    header("Location: gestion_usuarios.php");
    exit;

} catch (PDOException $e) {
    die("Error al eliminar el usuario: " . $e->getMessage());
}
?>