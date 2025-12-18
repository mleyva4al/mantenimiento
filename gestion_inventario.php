<?php


require 'includes/header.php';
require 'includes/admin_check.php';
require 'config/db.php';

$error = '';
$exito = '';

// --- LÓGICA DE POST (Si se envió un formulario) ---
try {
    
    if (isset($_POST['agregar_marca'])) {
        $nombre_marca = trim($_POST['nombre_marca']);
        if (!empty($nombre_marca)) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO marcas (nombre) VALUES (?)");
            $stmt->execute([$nombre_marca]);
            $exito = "Marca '$nombre_marca' agregada exitosamente (o ya existía).";
        } else {
            $error = "El nombre de la marca no puede estar vacío.";
        }
    }

   
    if (isset($_POST['agregar_modelo'])) {
        $id_marca = $_POST['id_marca'];
        $nombre_modelo = trim($_POST['nombre_modelo']);
        
        if (!empty($id_marca) && !empty($nombre_modelo)) {
            $stmt = $pdo->prepare("INSERT INTO modelos (id_marca, nombre) VALUES (?, ?)");
            $stmt->execute([$id_marca, $nombre_modelo]);
            $exito = "Modelo '$nombre_modelo' agregado exitosamente.";
        } else {
            $error = "Debe seleccionar una marca y escribir un nombre para el modelo.";
        }
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        $error = "Error: Ese modelo o marca ya existe.";
    } else {
        $error = "Error de base de datos: " . $e->getMessage();
    }
}


try {
   
    $marcas = $pdo->query("SELECT * FROM marcas ORDER BY nombre ASC")->fetchAll();

    
    $modelos = $pdo->query("
        SELECT modelos.id, modelos.nombre, marcas.nombre AS marca_nombre 
        FROM modelos 
        JOIN marcas ON modelos.id_marca = marcas.id 
        ORDER BY marcas.nombre, modelos.nombre
    ")->fetchAll();

} catch (PDOException $e) {
    $error = "Error fatal al cargar datos: " . $e->getMessage();
    $marcas = [];
    $modelos = [];
}
?>

<h2>(Admin) Gestión de Inventario (Marcas y Modelos)</h2>
<hr>

<?php if ($error): ?><p class="error-message"><?php echo $error; ?></p><?php endif; ?>
<?php if ($exito): ?><p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;"><?php echo $exito; ?></p><?php endif; ?>

<div style="display: flex; gap: 2rem;">

    <div style="flex: 1;">
        <h3>Gestión de Marcas</h3>
        
        <form action="gestion_inventario.php" method="POST" class="container" style="padding: 1.5rem; margin-left: 0;">
            <input type="hidden" name="agregar_marca" value="1">
            <label for="nombre_marca">Nombre de la Nueva Marca:</label>
            <input type="text" id="nombre_marca" name="nombre_marca" required>
            <button type="submit">Agregar Marca</button>
        </form>

        <div style="max-height: 400px; overflow-y: auto; margin-top: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius);">
            <table style="width: 100%;">
                <thead style="position: sticky; top: 0; background: var(--light-gray);">
                    <tr><th style="padding: 8px;">Marcas Actuales (<?php echo count($marcas); ?>)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($marcas as $marca): ?>
                        <tr><td style="padding: 5px 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($marca['nombre']); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="flex: 1;">
        <h3>Gestión de Modelos</h3>
        
        <form action="gestion_inventario.php" method="POST" class="container" style="padding: 1.5rem; margin-left: 0;">
            <input type="hidden" name="agregar_modelo" value="1">
            
            <label for="id_marca">Asignar a la Marca:</label>
            <select name="id_marca" id="id_marca" required>
                <option value="">Seleccione una marca...</option>
                <?php foreach ($marcas as $marca): ?>
                    <option value="<?php echo $marca['id']; ?>"><?php echo htmlspecialchars($marca['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <label for="nombre_modelo">Nombre del Nuevo Modelo:</label>
            <input type="text" id="nombre_modelo" name="nombre_modelo" placeholder="Ej: Elitebook 840 G5" required>
            
            <button type="submit">Agregar Modelo</button>
        </form>

        <div style="max-height: 400px; overflow-y: auto; margin-top: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius);">
            <table style="width: 100%;">
                <thead style="position: sticky; top: 0; background: var(--light-gray);">
                    <tr>
                        <th style="padding: 8px;">Marca</th>
                        <th style="padding: 8px;">Modelo (<?php echo count($modelos); ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modelos as $modelo): ?>
                        <tr>
                            <td style="padding: 5px 8px; border-bottom: 1px solid var(--border-color); font-weight: bold;"><?php echo htmlspecialchars($modelo['marca_nombre']); ?></td>
                            <td style="padding: 5px 8px; border-bottom: 1px solid var(--border-color);"><?php echo htmlspecialchars($modelo['nombre']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require 'includes/footer.php'; 
?>