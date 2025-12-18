<?php


require 'includes/header.php';
require 'includes/auth_check.php';
require 'config/db.php';


function hace_cuantos_dias($fecha_sql) {
    if (empty($fecha_sql)) return 'Fecha no disp.';
    $fecha_dictamen = new DateTime($fecha_sql);
    $hoy = new DateTime();
    $diferencia = $hoy->diff($fecha_dictamen);
    $dias = $diferencia->days;
    
    if ($dias == 0) return 'Hoy';
    if ($dias == 1) return 'Ayer (1 día)';
    return "Hace $dias días";
}



if ($_SESSION['role'] === 'admin') {
   
    try {
        $total_dictamenes = $pdo->query("SELECT COUNT(*) FROM dictamenes")->fetchColumn();
        $stmt_status = $pdo->query("SELECT status, COUNT(*) as total FROM dictamenes GROUP BY status");
        $conteo_status = $stmt_status->fetchAll(PDO::FETCH_KEY_PAIR);
        $stmt_delegacion = $pdo->query("SELECT delegacion, COUNT(*) as total FROM dictamenes GROUP BY delegacion");
        $conteo_delegacion = $stmt_delegacion->fetchAll(PDO::FETCH_ASSOC);

        $labels_grafica = [];
        $datos_grafica = [];
        foreach ($conteo_delegacion as $row) {
            $labels_grafica[] = $row['delegacion'];
            $datos_grafica[] = $row['total'];
        }
        $json_labels = json_encode($labels_grafica, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $json_datos = json_encode($datos_grafica, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    } catch (PDOException $e) {
        $error_stats = "Error al cargar estadísticas: " . $e->getMessage();
    }
} else {
   
    try {
        
        $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll();
        
       
        $solicitantes = $pdo->query("SELECT id, nombre AS nombre_completo FROM clientes ORDER BY nombre ASC")->fetchAll();

        
        
        $tareas_pendientes = [];
        $stmt_tareas = $pdo->prepare(
            "SELECT d.id, d.numero_serie, d.fecha_diagnostico, d.status,
             c.nombre AS nombre_solicitante

             FROM dictamenes d
             LEFT JOIN clientes c ON d.id_solicitante = c.id
             WHERE d.delegacion = ? 
             AND (d.status = 'En Proceso' OR d.status = 'Requiere Pieza')
             ORDER BY d.fecha_diagnostico ASC"
        );
        $stmt_tareas->execute([$_SESSION['delegacion']]);
        $tareas_pendientes = $stmt_tareas->fetchAll();

    } catch (PDOException $e) {
        $error_form = "Error al cargar datos: " . $e->getMessage();
        $marcas = $marcas ?? [];
        $solicitantes = $solicitantes ?? [];
        $tareas_pendientes = $tareas_pendientes ?? [];
    }
}
?>


<style>
    .dashboard-tecnico-layout {
        display: flex;
        flex-wrap: wrap; /* Para responsiveness en móviles */
        gap: 2rem;
    }
    .form-container {
        flex: 2; /* Que el formulario ocupe 2/3 del espacio */
        min-width: 400px; /* Ancho mínimo antes de que se rompa */
    }
    .tareas-container {
        flex: 1; /* La lista de tareas ocupa 1/3 */
        min-width: 300px;
        background-color: var(--light-gray);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1.5rem;
        max-height: 800px; /* Altura máxima con scroll */
        overflow-y: auto;
    }
    .tareas-container h3 {
        color: var(--udec-verde);
        margin-top: 0;
    }
    .tarea-card {
        background-color: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .tarea-info {
        font-size: 0.9em;
        line-height: 1.5;
    }
    .tarea-info strong {
        color: #333;
    }
    .tarea-info .dias {
        font-size: 0.9em;
        font-style: italic;
        color: #555;
    }
    .boton-atender {
        background-color: var(--udec-verde);
        color: white !important;
        padding: 0.5rem 0.75rem;
        text-decoration: none;
        border-radius: var(--radius);
        font-size: 0.9em;
        font-weight: 600;
        transition: background-color 0.2s ease;
    }
    .boton-atender:hover {
        background-color: var(--udec-verde-oscuro);
    }
</style>


<?php if ($_SESSION['role'] === 'admin'): ?>

    <h2>Dashboard de Administrador</h2>
    <hr>
    
    <?php if (isset($error_stats)): ?>
        <p class="error-message"><?php echo $error_stats; ?></p>
    <?php else: ?>
        
       
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
            <div style="flex: 1; min-width: 250px; padding: 20px; background: var(--light-gray); border-radius: var(--radius); border: 1px solid var(--border-color); text-align: center;">
                <h3 style="margin: 0; color: var(--udec-verde);">Total de Dictámenes</h3>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0;"><?php echo $total_dictamenes; ?></p>
            </div>
            <div style="flex: 1; min-width: 250px; padding: 20px; background: var(--light-gray); border-radius: var(--radius); border: 1px solid var(--border-color);">
                <h3 style="margin: 0; color: var(--udec-verde);">Desglose por Estatus</h3>
                <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
                    <?php if (!empty($conteo_status)): ?>
                        <?php foreach ($conteo_status as $status => $total): ?>
                            <li><strong><?php echo htmlspecialchars($status); ?>:</strong> <?php echo $total; ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No hay datos de estatus.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

      
        <div style="max-width: 500px; margin: 40px auto; padding: 20px; background: #fff; border-radius: var(--radius); box-shadow: var(--shadow);">
            <h3 style="text-align: center; color: var(--udec-verde);">Reportes por Delegación</h3>
            <canvas id="graficaDelegaciones"></canvas>
        </div>
        
    <?php endif; ?>


<?php else: ?>

    <h2>Dashboard del Técnico</h2>
    <h3>Delegación: <?php echo htmlspecialchars($_SESSION['delegacion']); ?></h3>
    <hr>
    
    <?php if (isset($error_form)): ?>
        <p class="error-message"><?php echo $error_form; ?></p>
    <?php endif; ?>

 
    <div class="dashboard-tecnico-layout">
        
       
        <div class="form-container">
            <h3>Crear Nuevo Dictamen</h3>
            
            <form action="guardar_dictamen.php" method="POST" enctype="multipart/form-data">
        
                <input type="hidden" name="delegacion" value="<?php echo htmlspecialchars($_SESSION['delegacion']); ?>">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

                
                <label for="id_solicitante">Asignar a Solicitante:</label>
                <select id="id_solicitante" name="id_solicitante">
                    <option value="">-- Ninguno (o Solicitante General) --</option>
                    <?php foreach ($solicitantes as $solicitante): ?>
                        <option value="<?php echo $solicitante['id']; ?>">
                            <?php echo htmlspecialchars($solicitante['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                

                <label for="centro_trabajo">Centro de Trabajo:</label>
                <input type="text" id="centro_trabajo" name="centro_trabajo">

                <label for="tipo_equipo">Tipo de Equipo:</label>
                <select id="tipo_equipo" name="tipo_equipo">
                    <option value="">Seleccione...</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Impresora">Impresora</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Otro">Otro</option>
                </select>
                
                <label for="select_marca">Marca:</label>
                <select name="marca" id="select_marca" required>
                    <option value="">Seleccione una marca</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo $marca['id']; ?>"><?php echo htmlspecialchars($marca['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="select_modelo">Modelo (API):</label>
                <select name="modelo" id="select_modelo" required disabled>
                    <option value="">Seleccione una marca primero</option>
                </select>

                <label for="numero_serie">Número de Serie:</label>
                <input type="text" id="numero_serie" name="numero_serie" required>

                <label for="numero_inventario">Número de Inventario:</label>
                <input type="text" id="numero_inventario" name="numero_inventario">
                
                <label for="fecha_diagnostico">Fecha del Diagnóstico:</label>
                <input type="date" id="fecha_diagnostico" name="fecha_diagnostico" required value="<?php echo date('Y-m-d'); ?>">

                <label for="diagnostico">Diagnóstico / Observaciones:</label>
                <textarea id="diagnostico" name="diagnostico" placeholder="Describa la falla y el diagnóstico..."></textarea>
                
                <label for="fotos">Adjuntar Fotos (Opcional):</label>
                <input type="file" name="fotos[]" id="fotos" multiple accept="image/png, image/jpeg, image/gif">

                <button type="submit">Guardar Dictamen</button>
            </form>
        </div>

        
        <div class="tareas-container">
            <h3>Mis Tareas Pendientes (<?php echo count($tareas_pendientes); ?>)</h3>

            <?php if (isset($error_tareas)): ?>
                <p class="error-message"><?php echo $error_tareas; ?></p>
            <?php endif; ?>
            
            <?php if (empty($tareas_pendientes)): ?>
                <p>¡Felicidades! No tienes dictámenes pendientes.</p>
            <?php else: ?>
                <?php foreach ($tareas_pendientes as $tarea): ?>
                    <div class="tarea-card">
                        <div class="tarea-info">
                           
                            <strong>Solicitante:</strong> <?php echo htmlspecialchars($tarea['nombre_solicitante'] ?? 'N/A'); ?><br>
                            <strong>Serie:</strong> <?php echo htmlspecialchars($tarea['numero_serie']); ?><br>
                            <span class="dias">(<?php echo hace_cuantos_dias($tarea['fecha_diagnostico']); ?>)</span>
                        </div>
                        <a href="editar_dictamen.php?id=<?php echo $tarea['id']; ?>" class="boton-atender">Atender</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
<?php endif; ?>


<?php

if ($_SESSION['role'] === 'admin') {
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo "
    <script>
        const ctx = document.getElementById('graficaDelegaciones');
        
        // Evitar que Chart.js intente renderizar si no hay datos
        if (typeof $json_datos !== 'undefined' && $json_datos.length > 2) { // > 2 porque json_encode de array vacío es '[]'
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: $json_labels,
                    datasets: [{
                        label: '# de Reportes',
                        data: $json_datos,
                        backgroundColor: [
                            '#006241', // Verde UdeC
                            '#FDBB30', // Oro UdeC
                            '#B5B5B5', // Gris
                            '#2E8B57'  // Verde Mar
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    }
                }
            });
        } else {
            // Si no hay datos, solo muestra un mensaje
             if (ctx) {
                ctx.getContext('2d').fillText('No hay datos suficientes para mostrar la gráfica.', 10, 50);
             }
        }
    </script>
    ";
}

require 'includes/footer.php';
?>