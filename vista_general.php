<?php

require 'includes/header.php'; 
require 'includes/auth_check.php'; 
require 'config/db.php';

function get_status_class($status) {
    switch (strtolower($status)) {
        case 'terminado':
            return 'status-terminado';
        case 'requiere pieza':
            return 'status-requiere-pieza';
        case 'baja ( irreparable)':
            return 'status-baja';
        case 'en proceso':
        default:
            return 'status-en-proceso';
    }
}




$filtro_delegacion = $_GET['delegacion'] ?? '';
$filtro_status = $_GET['status'] ?? '';
$filtro_fecha_inicio = $_GET['fecha_inicio'] ?? '';
$filtro_fecha_fin = $_GET['fecha_fin'] ?? '';


$sql_base = "
    SELECT 
        d.id, d.delegacion, d.marca, d.numero_serie, d.fecha_diagnostico, d.status,
        u.nombre_completo AS nombre_solicitante 
    FROM dictamenes d
    LEFT JOIN usuarios u ON d.id_solicitante = u.id
";


$params = [];
$where_clauses = []; 
$titulo = "Vista General de Dictámenes";

//  APLICAR FILTRO DE SEGURIDAD 
if ($_SESSION['role'] === 'tecnico') {
 
    $where_clauses[] = "d.delegacion = ?";
    $params[] = $_SESSION['delegacion'];
    $titulo = "Mis Dictámenes (Delegación: " . htmlspecialchars($_SESSION['delegacion']) . ")";
} else {
  
    if (!empty($filtro_delegacion)) {
        $where_clauses[] = "d.delegacion = ?";
        $params[] = $filtro_delegacion;
    }
}

if (!empty($filtro_status)) {
    $where_clauses[] = "d.status = ?";
    $params[] = $filtro_status;
}
if (!empty($filtro_fecha_inicio)) {
    $where_clauses[] = "d.fecha_diagnostico >= ?";
    $params[] = $filtro_fecha_inicio;
}
if (!empty($filtro_fecha_fin)) {
    $where_clauses[] = "d.fecha_diagnostico <= ?";
    $params[] = $filtro_fecha_fin;
}


if (!empty($where_clauses)) {
    $sql_base .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql_base .= " ORDER BY d.id DESC";


try {
    $stmt = $pdo->prepare($sql_base);
    $stmt->execute($params);
    $dictamenes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error al consultar dictámenes: " . $e->getMessage();
    $dictamenes = [];
}


try {
    $lista_delegaciones = [];
    if ($_SESSION['role'] === 'admin') {
        $lista_delegaciones = $pdo->query("SELECT DISTINCT delegacion FROM dictamenes WHERE delegacion IS NOT NULL AND delegacion != '' ORDER BY delegacion")
                                ->fetchAll(PDO::FETCH_COLUMN);
    }
    $lista_status = $pdo->query("SELECT DISTINCT status FROM dictamenes WHERE status IS NOT NULL AND status != '' ORDER BY status")
                        ->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $lista_delegaciones = [];
    $lista_status = [];
}
?>


<style>
    .filtro-container {
        background-color: var(--light-gray);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: flex-end;
    }
    .filtro-grupo {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }
    .filtro-grupo label {
        font-weight: 600;
        font-size: 0.9em;
        margin-bottom: 5px;
    }
    .filtro-grupo select,
    .filtro-grupo input[type="date"] {
        padding: 0.5rem;
    }
    .filtro-container button,
    .filtro-container a.boton-limpiar {
        padding: 0.5rem 1rem;
        font-size: 0.9em;
        text-decoration: none;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
    }
    .filtro-container button {
        background-color: var(--udec-verde);
    }

    
    .status-badge {
        padding: 3px 8px;
        border-radius: var(--radius);
        font-size: 0.85em;
        font-weight: 600;
        color: #fff;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
    }
    .status-en-proceso {
        background-color: var(--primary-color); /* Azul */
    }
    .status-terminado {
        background-color: var(--udec-verde); /* Verde UdeC */
    }
    .status-requiere-pieza {
        background-color: #fd7e14; /* Naranja */
    }
    .status-baja {
        background-color: #dc3545; /* Rojo */
    }
</style>

<h2><?php echo $titulo; ?></h2>


<div class="filtro-container">
    <form method="GET" action="vista_general.php" style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: flex-end;">
        
      
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="filtro-grupo">
                <label for="delegacion">Delegación:</label>
                <select name="delegacion" id="delegacion">
                    <option value="">Todas</option>
                    <?php foreach ($lista_delegaciones as $del): ?>
                        <option value="<?php echo htmlspecialchars($del); ?>" <?php echo ($filtro_delegacion == $del) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($del); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

      
        <div class="filtro-grupo">
            <label for="status">Estatus:</label>
            <select name="status" id="status">
                <option value="">Todos</option>
                <?php foreach ($lista_status as $stat): ?>
                    <option value="<?php echo htmlspecialchars($stat); ?>" <?php echo ($filtro_status == $stat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($stat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

     
        <div class="filtro-grupo">
            <label for="fecha_inicio">Desde:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($filtro_fecha_inicio); ?>">
        </div>
        <div class="filtro-grupo">
            <label for="fecha_fin">Hasta:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($filtro_fecha_fin); ?>">
        </div>

        <div class="filtro-grupo">
            <button type="submit">Filtrar</button>
        </div>
    </form>
 
    <div class="filtro-grupo">
        <a href="vista_general.php" class="boton-limpiar">Limpiar Filtros</a>
    </div>
</div>


<hr>


<table id="tabla_general" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Folio</th>
            <th>Delegación</th>
            <th>Solicitante</th> 
            <th>Marca</th>
            <th>No. Serie</th>
            <th>Fecha Diag.</th>
            <th>Estatus</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dictamenes as $d): ?>
        <tr>
            <td><?php echo str_pad($d['id'], 6, '0', STR_PAD_LEFT); ?></td>
            <td><?php echo htmlspecialchars($d['delegacion']); ?></td>
            
        
            <td><?php echo htmlspecialchars($d['nombre_solicitante'] ?? 'N/A'); ?></td>
            
            <td><?php echo htmlspecialchars($d['marca']); ?></td>
            <td><?php echo htmlspecialchars($d['numero_serie']); ?></td>
            <td><?php echo htmlspecialchars($d['fecha_diagnostico']); ?></td>
            <td>
                <?php $status_class = get_status_class($d['status']); ?>
                <span class="status-badge <?php echo $status_class; ?>">
                    <?php echo htmlspecialchars($d['status']); ?>
                </span>
            </td>
            <td>
                <a href="generar_etiqueta.php?id=<?php echo $d['id']; ?>" target="_blank" title="Imprimir Etiqueta QR">🏷️</a>
                <a href="generar_pdf.php?id=<?php echo $d['id']; ?>" target="_blank" title="Ver PDF">📄</a>
                <a href="editar_dictamen.php?id=<?php echo $d['id']; ?>" title="Editar Dictamen">✏️</a>
                <a href="eliminar_dictamen.php?id=<?php echo $d['id']; ?>" title="Eliminar Dictamen" 
                   onclick="return confirm('¿Estás seguro de que deseas eliminar este dictamen (Folio: <?php echo $d['id']; ?>)? Esta acción no se puede deshacer.');">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require 'includes/footer.php'; 
?>