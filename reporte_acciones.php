<?php
// reporte_acciones.php
// Panel de Admin para ver la bitácora de auditoría.

require 'includes/header.php';
require 'includes/admin_check.php'; // ¡Protegido!
require 'config/db.php';

try {
    // Consultar el log, uniendo con la tabla de usuarios para obtener el nombre
    $stmt = $pdo->query("
        SELECT 
            la.id,
            la.fecha_hora,
            la.accion AS accion_realizada, 
            la.ip_direccion,
            u.nombre_completo 
        FROM logs_acciones la
        JOIN usuarios u ON la.id_usuario = u.id
        ORDER BY la.fecha_hora DESC
        LIMIT 500
    "); // Limitar a 500 para no sobrecargar
    $logs = $stmt->fetchAll();

} catch (PDOException $e) {
    // Es buena práctica no mostrar el error crudo al usuario final
    echo "Error al cargar el reporte: " . $e->getMessage();
    $logs = [];
}
?>

<h2>(Admin) Bitácora de Acciones del Sistema</h2>
<p>Mostrando las últimas 500 acciones registradas.</p>
<hr>

<!-- 
    ID="logsTable" -> Para que el footer lo ordene por fecha (Col 0, desc).
    CLASS="display" -> Para que el footer lo encuentre y le aplique DataTables + Botones.
-->
<table id="logsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Fecha y Hora</th>
            <th>Usuario (Técnico/Admin)</th>
            <th>Acción Realizada</th>
            <th>Dirección IP</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?php echo htmlspecialchars($log['fecha_hora']); ?></td>
            <td><?php echo htmlspecialchars($log['nombre_completo']); ?></td>
            <td><?php echo htmlspecialchars($log['accion_realizada']); ?></td>
            <td><?php echo htmlspecialchars($log['ip_direccion']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php

require 'includes/footer.php'; 
?>