<?php

require 'includes/header.php';
require 'includes/auth_check.php';
require 'config/db.php';

$id_dictamen = $_GET['id'] ?? null;
$error_fotos = '';
$exito_fotos = '';
$exito_notas = ''; 


if (!filter_var($id_dictamen, FILTER_VALIDATE_INT) || $id_dictamen <= 0) {
    echo "<h1>Error</h1><p>No se proporcionó un ID de dictamen válido.</p>";
    require 'includes/footer.php';
    exit;
}

try {
    $sql = "
        SELECT
            d.*,
            u.nombre_completo AS nombre_solicitante_db
        FROM dictamenes d
        LEFT JOIN usuarios u ON d.id_solicitante = u.id
        WHERE d.id = :id_dictamen
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_dictamen' => $id_dictamen]);
    $dictamen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dictamen) {
        echo "<h1>Error</h1><p>Dictamen no encontrado.</p>";
        require 'includes/footer.php';
        exit;
    }

  
    $nombre_solicitante_mostrar = $dictamen['nombre_solicitante_db'] ?? 'Desconocido';

    
    if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $dictamen['id_usuario']) {
        echo "<h1>Acceso Denegado</h1><p>No tienes permiso para editar este dictamen.</p>";
        require 'includes/footer.php';
        exit;
    }

   
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll();

   
    $stmt_fotos = $pdo->prepare("SELECT id, ruta_archivo FROM dictamen_fotos WHERE id_dictamen = :id_dictamen ORDER BY fecha_subida DESC");
    $stmt_fotos->execute([':id_dictamen' => $id_dictamen]);
    $fotos_adjuntas = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

 
    $stmt_notas = $pdo->prepare("
        SELECT 
            dn.nota, 
            dn.fecha_hora, 
            u.nombre_completo 
        FROM dictamen_notas dn
        JOIN usuarios u ON dn.id_usuario = u.id
        WHERE dn.id_dictamen = :id_dictamen
        ORDER BY dn.fecha_hora DESC
    ");
    $stmt_notas->execute([':id_dictamen' => $id_dictamen]);
    $notas_bitacora = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);

  
    if (isset($_GET['foto_exito'])) $exito_fotos = "Foto eliminada exitosamente.";
    if (isset($_GET['foto_error'])) $error_fotos = "Error al eliminar la foto.";
    if (isset($_GET['nota_exito'])) $exito_notas = "Nota guardada exitosamente.";

} catch (PDOException $e) {
    echo "Error al cargar datos: " . htmlspecialchars($e->getMessage());
    require 'includes/footer.php';
    exit;
}
?>

<h2>Editar Dictamen - Folio: <?php echo str_pad($dictamen['id'], 6, '0', STR_PAD_LEFT); ?></h2>
<hr>

<?php if ($error_fotos): ?>
<p class="error-message" style="color: #900; background-color: #fdd; border: 1px solid #900; padding: 10px; border-radius: 8px;">
    <?php echo $error_fotos; ?>
</p>
<?php endif; ?>

<?php if ($exito_fotos): ?>
<p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;">
    <?php echo $exito_fotos; ?>
</p>
<?php endif; ?>

<form action="actualizar_dictamen.php" method="POST" enctype="multipart/form-data">

  
    <input type="hidden" name="id_dictamen" value="<?php echo htmlspecialchars($dictamen['id']); ?>">
    <input type="hidden" name="id_solicitante" value="<?php echo htmlspecialchars($dictamen['id_solicitante'] ?? ''); ?>">
    <input type="hidden" name="id_usuario_tecnico" value="<?php echo htmlspecialchars($dictamen['id_usuario']); ?>">
    <input type="hidden" name="delegacion" value="<?php echo htmlspecialchars($dictamen['delegacion']); ?>">

    
    <label for="nombre_solicitante_display">Solicitante:</label>
    <input type="text" id="nombre_solicitante_display" 
           value="<?php echo htmlspecialchars($nombre_solicitante_mostrar); ?>" disabled>

    <label for="delegacion_display">Delegación:</label>
    <input type="text" id="delegacion_display" 
           value="<?php echo htmlspecialchars($dictamen['delegacion']); ?>" disabled>

  
    <label for="status">Estatus Actual:</label>
    <select id="status" name="status" required>
        <option value="En Proceso" <?php echo ($dictamen['status'] == 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
        <option value="Requiere Pieza" <?php echo ($dictamen['status'] == 'Requiere Pieza') ? 'selected' : ''; ?>>Requiere Pieza</option>
        <option value="Terminado" <?php echo ($dictamen['status'] == 'Terminado') ? 'selected' : ''; ?>>Terminado</option>
        <option value="Baja (Irreparable)" <?php echo ($dictamen['status'] == 'Baja (Irreparable)') ? 'selected' : ''; ?>>Baja (Irreparable)</option>
    </select>

    <label for="centro_trabajo">Centro de Trabajo:</label>
    <input type="text" id="centro_trabajo" name="centro_trabajo" 
           value="<?php echo htmlspecialchars($dictamen['centro_trabajo']); ?>">

    <label for="tipo_equipo">Tipo de Equipo:</label>
    <select id="tipo_equipo" name="tipo_equipo">
        <option value="">Seleccione...</option>
        <?php
        $tipos = ['Laptop','Desktop','Impresora','Monitor','Otro'];
        foreach ($tipos as $tipo) {
            $sel = ($dictamen['tipo_equipo'] == $tipo) ? 'selected' : '';
            echo "<option value='$tipo' $sel>$tipo</option>";
        }
        ?>
    </select>

    <label for="select_marca">Marca:</label>
    <select name="marca" id="select_marca" required>
        <option value="">Seleccione una marca</option>
        <?php foreach ($marcas as $marca): ?>
            <option value="<?php echo htmlspecialchars($marca['nombre']); ?>" 
                <?php echo ($dictamen['marca'] == $marca['nombre']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($marca['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="modelo">Modelo:</label>
    <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($dictamen['modelo']); ?>">

    <label for="numero_serie">Número de Serie:</label>
    <input type="text" id="numero_serie" name="numero_serie" 
           value="<?php echo htmlspecialchars($dictamen['numero_serie']); ?>" required>

    <label for="numero_inventario">Número de Inventario:</label>
    <input type="text" id="numero_inventario" name="numero_inventario" 
           value="<?php echo htmlspecialchars($dictamen['numero_inventario']); ?>">

    <label for="fecha_diagnostico">Fecha del Diagnóstico:</label>
    <input type="date" id="fecha_diagnostico" name="fecha_diagnostico" 
           required value="<?php echo htmlspecialchars($dictamen['fecha_diagnostico']); ?>">

    <label for="diagnostico">Diagnóstico / Observaciones:</label>
    <textarea id="diagnostico" name="diagnostico" 
              placeholder="Describa la falla y el diagnóstico..."><?php echo htmlspecialchars($dictamen['diagnostico']); ?></textarea>

    
    <label for="nuevas_fotos">Adjuntar Nuevas Fotos (Opcional):</label>
    <input type="file" name="fotos[]" id="nuevas_fotos" multiple accept="image/png, image/jpeg, image/webp">

    <button type="submit" class="boton-primario">Actualizar Dictamen</button>
</form>

<?php require 'includes/footer.php'; ?>
