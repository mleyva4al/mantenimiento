<?php


session_start();
require 'includes/auth_check.php';
require 'config/db.php';


function procesar_fotos($pdo, $id_dictamen, $archivos_fotos) {
    $ruta_subida = __DIR__ . '/uploads/'; 
    if (!is_dir($ruta_subida)) mkdir($ruta_subida, 0755, true);

    $fotos_subidas = 0;
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

   
    if (!isset($archivos_fotos['name']) || !is_array($archivos_fotos['name'])) {
        return 0;
    }

    foreach ($archivos_fotos['name'] as $indice => $nombre_original) {
        if (empty($nombre_original) || $archivos_fotos['error'][$indice] !== UPLOAD_ERR_OK) {
            continue;
        }

        $archivo_temporal = $archivos_fotos['tmp_name'][$indice];
        $tipo_archivo = $archivos_fotos['type'][$indice];
        $tamano_archivo = $archivos_fotos['size'][$indice];

        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            error_log("Archivo omitido: $nombre_original (tipo no permitido: $tipo_archivo)");
            continue;
        }
        if ($tamano_archivo > 5 * 1024 * 1024) {
            error_log("Archivo omitido: $nombre_original (demasiado grande)");
            continue;
        }

        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_unico = uniqid('', true) . '_' . time() . '.' . strtolower($extension);
        $ruta_destino = $ruta_subida . $nombre_unico;

        if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
            try {
                $sql = "INSERT INTO dictamen_fotos (id_dictamen, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_dictamen, $nombre_unico, $ruta_destino]);
                $fotos_subidas++;
            } catch (PDOException $e) {
                error_log("Error al guardar foto en BD: " . $e->getMessage());
            }
        } else {
            error_log("Error al mover el archivo subido: $nombre_original");
        }
    }
    return $fotos_subidas;
}
// -------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vista_general.php');
    exit;
}

$id_dictamen = $_POST['id_dictamen'] ?? null;
if (!filter_var($id_dictamen, FILTER_VALIDATE_INT) || $id_dictamen <= 0) {
    die("Error: ID de dictamen no válido.");
}

// Recolectar datos
$id_solicitante_raw = $_POST['id_solicitante'] ?? '';
$id_solicitante = ($id_solicitante_raw === '') ? null : (int)$id_solicitante_raw;

$centro_trabajo = trim($_POST['centro_trabajo'] ?? '');
$tipo_equipo = trim($_POST['tipo_equipo'] ?? '');
$id_marca = $_POST['marca'] ?? null;
$modelo = trim($_POST['modelo'] ?? '');
$numero_serie = trim($_POST['numero_serie'] ?? '');
$numero_inventario = trim($_POST['numero_inventario'] ?? '');
$fecha_diagnostico = $_POST['fecha_diagnostico'] ?? date('Y-m-d');
$status = trim($_POST['status'] ?? 'En Proceso');
$diagnostico = trim($_POST['diagnostico'] ?? '');

// Obtener nombre de la marca
try {
    $stmt_marca = $pdo->prepare("SELECT nombre FROM marcas WHERE id = ?");
    $stmt_marca->execute([$id_marca]);
    $marca_nombre = $stmt_marca->fetchColumn() ?: 'Marca Desconocida';
} catch (PDOException $e) {
    die("Error al consultar marca: " . $e->getMessage());
}

// Ejecutar UPDATE
try {
    $sql = "UPDATE dictamenes SET
                id_solicitante = ?,
                centro_trabajo = ?,
                tipo_equipo = ?,
                marca = ?,
                modelo = ?,
                numero_serie = ?,
                numero_inventario = ?,
                fecha_diagnostico = ?,
                status = ?,
                diagnostico = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_solicitante,
        $centro_trabajo,
        $tipo_equipo,
        $marca_nombre,
        $modelo,
        $numero_serie,
        $numero_inventario,
        $fecha_diagnostico,
        $status,
        $diagnostico,
        $id_dictamen
    ]);

    // Procesar fotos si existen
    if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
        procesar_fotos($pdo, $id_dictamen, $_FILES['fotos']);
    }

    header("Location: vista_general.php");
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die("Error: El número de serie o inventario que intentas guardar ya existe en otro registro. <a href='editar_dictamen.php?id=$id_dictamen'>Volver</a>");
    } else {
        die("Error al actualizar la base de datos: " . $e->getMessage());
    }
}
