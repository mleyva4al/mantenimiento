<?php
session_start();
require 'includes/auth_check.php'; 
require 'config/db.php';

$id_dictamen = $_GET['id'] ?? null;

if (!filter_var($id_dictamen, FILTER_VALIDATE_INT) || $id_dictamen <= 0) {
    die("Error: ID de dictamen no válido.");
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            d.id, 
            d.numero_serie, 
            d.numero_inventario, 
            u.nombre_completo AS nombre_solicitante
        FROM dictamenes d
        LEFT JOIN usuarios u ON d.id_solicitante = u.id
        WHERE d.id = ?
    ");
    $stmt->execute([$id_dictamen]);
    $dictamen = $stmt->fetch();

    if (!$dictamen) {
        die("Dictamen no encontrado.");
    }

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}


$url_base_servidor = "http://localhost";
$url_para_qr = "$url_base_servidor/mantenimiento/editar_dictamen.php?id=" . $id_dictamen;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Folio <?php echo $id_dictamen; ?></title>
    
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        /* Estilos generales */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Estilos de la Etiqueta */
        .etiqueta-container {
            width: 400px;
            background-color: #fff;
            border: 2px dashed #999;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 columnas */
            gap: 10px;
            align-items: center;
        }
        
        .etiqueta-info {
            line-height: 1.6;
        }
        .etiqueta-info h3 {
            margin: 0 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            color: <?php echo $color_verde_udec = "#006241"; // Verde UdeC ?>;
        }
        .etiqueta-info strong {
            display: inline-block;
            min-width: 60px; /* Alineación de labels */
            color: #333;
        }
        
        /* Contenedor del QR */
        #qrcode {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border: 5px solid <?php echo $color_verde_udec; ?>;
            padding: 5px;
            box-sizing: border-box; /* Para que el borde no sume al tamaño */
        }
        
        /* Botón de Imprimir */
        .boton-imprimir {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background-color: <?php echo $color_verde_udec; ?>;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* --- ESTILOS DE IMPRESIÓN --- */
        @media print {
            body {
                background-color: #fff;
            }
            .boton-imprimir {
                display: none;
            }
            .etiqueta-container {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="etiqueta-container">
       
        <div class="etiqueta-info">
            <h3>Folio: <?php echo str_pad($dictamen['id'], 6, '0', STR_PAD_LEFT); ?></h3>
            <strong>N/S:</strong> <?php echo htmlspecialchars($dictamen['numero_serie']); ?><br>
            <strong>Inv:</strong> <?php echo htmlspecialchars($dictamen['numero_inventario']); ?><br>
            <strong>Sol:</strong> <?php echo htmlspecialchars($dictamen['nombre_solicitante'] ?? 'Desconocido'); ?>
        </div>
        
       
        <div id="qrcode"></div>
    </div>

    <button onclick="window.print();" class="boton-imprimir">Imprimir Etiqueta</button>


    <script type="text/javascript">
        var url_para_qr = "<?php echo $url_para_qr; ?>";
        
        new QRCode(document.getElementById("qrcode"), {
            text: url_para_qr,
            width: 130,
            height: 130,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>

</body>
</html>
