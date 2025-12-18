<?php
// includes/funciones.php
// Este archivo contendrá funciones útiles que usaremos en todo el sitio.

/**
 * Registra una acción de auditoría en la tabla logs_acciones.
 *
 * @param PDO $pdo La conexión a la base de datos.
 * @param string $accion La descripción de lo que sucedió.
 */
function log_accion($pdo, $accion) {
    // No queremos registrar acciones si la sesión no está iniciada
    if (!isset($_SESSION['user_id'])) {
        return; 
    }

    try {
        $id_usuario = $_SESSION['user_id'];
        $ip_direccion = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $sql = "INSERT INTO logs_acciones (id_usuario, accion, ip_direccion) 
                VALUES (?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario, $accion, $ip_direccion]);

    } catch (PDOException $e) {
        // Si falla el log, no detenemos la aplicación.
        // Solo lo registramos en el log de errores del servidor.
        error_log("Error al registrar acción en bitácora: " . $e->getMessage());
    }
}
?>