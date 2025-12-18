<?php
// eliminar_dictamen.php (Versión 3.2 - Corregida)

session_start();
require 'includes/auth_check.php'; 
require 'config/db.php';         
require_once 'includes/funciones.php';

$id_dictamen = $_GET['id'] ?? null;

if (!filter_var($id_dictamen, FILTER_VALIDATE_INT) || $id_dictamen <= 0) {
    die("Error: ID de dictamen no válido.");
}

try {

    // ✔ CONSULTA CORREGIDA:
    // Los solicitantes ahora están en la tabla "usuarios"
    // y la columna correcta es "nombre_completo"
    $stmt = $pdo->prepare("
        SELECT 
            d.delegacion, 
            u.nombre_completo AS nombre_solicitante, 
            d.numero_serie
        FROM dictamenes d
        LEFT JOIN usuarios u ON d.id_solicitante = u.id
        WHERE d.id = ?
    ");
    $stmt->execute([$id_dictamen]);
    $dictamen = $stmt->fetch();

    if (!$dictamen) {
        die("Error: Dictamen no encontrado.");
    }

    // Validación de permisos
    if ($_SESSION['role'] === 'tecnico' && $dictamen['delegacion'] !== $_SESSION['delegacion']) {
        die("Acceso Denegado. No tienes permiso para eliminar este dictamen.");
    }
    
    // Registrar acción en bitácora
    $log_accion_descripcion = "Eliminó el dictamen ID #{$id_dictamen} (Solicitante: {$dictamen['nombre_solicitante']}, Serie: {$dictamen['numero_serie']}).";
    log_accion($pdo, $log_accion_descripcion);

    // Eliminar dictamen
    $sql = "DELETE FROM dictamenes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_dictamen]);

    header("Location: vista_general.php");
    exit;

} catch (PDOException $e) {
    die("Error al eliminar el dictamen: " . $e->getMessage());
}
?>
