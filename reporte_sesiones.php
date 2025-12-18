<?php


require 'includes/header.php';
require 'includes/admin_check.php'; 
require 'config/db.php';

$error = '';
$logs = [];

try {
    // Consulta JOIN para obtener los logs y el nombre completo del usuario
    $sql = "
        SELECT 
            ls.id, 
            ls.usuario_ingresado, 
            ls.fecha_hora, 
            ls.es_exitoso, 
            ls.ip_direccion,
            u.nombre_completo AS nombre_usuario_real
        FROM logs_sesion ls
        LEFT JOIN usuarios u ON ls.id_usuario = u.id
        ORDER BY ls.fecha_hora DESC
        LIMIT 200 -- Limitar a los 200 logs más recientes
    ";
    
    $stmt = $pdo->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al cargar los logs de sesión: " . $e->getMessage();
}

?>

<h2>(Admin) Reporte de Actividad de Sesiones</h2>
<p class="subtitle">Muestra los 200 intentos de inicio de sesión más recientes.</p>

<?php if ($error): ?><p class="error-message"><?php echo $error; ?></p><?php endif; ?>

<div style="overflow-x: auto;">
    <table id="logsTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Estado</th>
                <th>Usuario (Intentado)</th>
                <th>Usuario (Real)</th>
                <th>Dirección IP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['fecha_hora'])); ?></td>
                    <td style="font-weight: bold; color: <?php echo $log['es_exitoso'] ? 'green' : 'red'; ?>;">
                        <?php echo $log['es_exitoso'] ? 'ÉXITO' : 'FALLIDO'; ?>
                    </td>
                    <td><?php echo htmlspecialchars($log['usuario_ingresado']); ?></td>
                    <td><?php echo htmlspecialchars($log['nombre_usuario_real'] ?? 'N/A (Fallido)'); ?></td>
                    <td><?php echo htmlspecialchars($log['ip_direccion'] ?? 'Desconocida'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php 

require 'includes/footer.php'; 

?>
