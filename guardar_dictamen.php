<?php
session_start();
require 'includes/auth_check.php';
require 'config/db.php';


function procesar_fotos($pdo, $id_dictamen, $archivos_fotos) {
    $ruta_subida = 'uploads/';
    $fotos_subidas = 0;
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

    foreach ($archivos_fotos['name'] as $indice => $nombre_original) {
        if (!empty($nombre_original) && $archivos_fotos['error'][$indice] === UPLOAD_ERR_OK) {
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
            $nombre_unico = uniqid() . '_' . time() . '.' . strtolower($extension);
            $ruta_destino = $ruta_subida . $nombre_unico;

            if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
                try {
                    $sql = "INSERT INTO dictamen_fotos (id_dictamen, nombre_archivo, ruta_archivo) 
                            VALUES (?, ?, ?)";
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
    }
    return $fotos_subidas;
}




if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}


$id_usuario = $_POST['id_usuario'] ?? null;
$delegacion = $_POST['delegacion'] ?? 'N/A';
$id_solicitante = $_POST['id_solicitante'] ?? null;

$centro_trabajo = trim($_POST['centro_trabajo'] ?? '');
$tipo_equipo = trim($_POST['tipo_equipo'] ?? '');
$id_marca = $_POST['marca'] ?? null;
$modelo = trim($_POST['modelo'] ?? '');
$numero_serie = trim($_POST['numero_serie'] ?? '');
$numero_inventario = trim($_POST['numero_inventario'] ?? '');
$diagnostico = trim($_POST['diagnostico'] ?? '');
$fecha_diagnostico = $_POST['fecha_diagnostico'] ?? date('Y-m-d');

if (empty($id_usuario) || empty($delegacion) || empty($id_solicitante) || empty($numero_serie) || empty($id_marca)) {
    die("Error: Faltan datos esenciales (ID Solicitante o Serie). <a href='dashboard.php'>Volver</a>");
}


try {
    $stmt_marca = $pdo->prepare("SELECT nombre FROM marcas WHERE id = ?");
    $stmt_marca->execute([$id_marca]);
    $marca_nombre = $stmt_marca->fetchColumn();
    if (!$marca_nombre) $marca_nombre = 'Marca Desconocida';
} catch (PDOException $e) {
    die("Error al consultar marca: " . $e->getMessage());
}


try {
    $sql = "INSERT INTO dictamenes 
            (id_usuario, id_solicitante, delegacion, centro_trabajo, 
             tipo_equipo, marca, modelo, numero_serie, 
             numero_inventario, diagnostico, fecha_diagnostico) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $id_usuario,
        $id_solicitante,
        $delegacion,
        $centro_trabajo,
        $tipo_equipo,
        $marca_nombre,
        $modelo,
        $numero_serie,
        $numero_inventario,
        $diagnostico,
        $fecha_diagnostico
    ]);

    $ultimo_id = $pdo->lastInsertId();

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { 
        die("Error: El número de serie o inventario ya existe. <a href='dashboard.php'>Volver</a>");
    } else {
        die("Error al guardar en la base de datos: " . $e->getMessage());
    }
}

if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
    procesar_fotos($pdo, $ultimo_id, $_FILES['fotos']);
}

header("Location: generar_pdf.php?id=" . $ultimo_id);
exit;
?>
