<?php
/**
 * templates/pdf_template.php
 *
 * Plantilla visual del dictamen - ESTILO CARTA FORMAL (Sin alterar BD)
 * Asume que $dictamen (array) y $_SESSION (array) existen.
 */

// ---- Colores Institucionales ----
$color_verde_udec = "#006241";
$color_texto = "#222222";

// Función para formatear la fecha a español (ej: 11 de Noviembre de 2025)
function formatearFechaEspanol($fecha_sql) {
    $timestamp = strtotime($fecha_sql);
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $dia = date('d', $timestamp);
    $mes = $meses[(int)date('m', $timestamp)];
    $ano = date('Y', $timestamp);
    return "$dia de $mes de $ano";
}

$fecha_dictamen_formateada = formatearFechaEspanol($dictamen['fecha_diagnostico']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dictamen de Mantenimiento</title>
    <style>
        body {
            font-family: 'helvetica', 'sans-serif'; /* Fuente formal */
            font-size: 11pt; /* Tamaño de letra de carta */
            color: <?php echo $color_texto; ?>;
            line-height: 1.6; 
        }

        /* --- Encabezado de la carta (Fecha y Folio) --- */
        .header-info {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 30px;
        }
        .header-info .folio {
            font-weight: bold;
            font-size: 11pt;
            color: <?php echo $color_verde_udec; ?>;
        }

        /* --- Títulos (A quien corresponda, Asunto) --- */
        .recipient {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 20px;
        }
        .subject {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 20px;
            text-align: right;
        }

        /* --- Párrafos --- */
        p.main-text {
            margin: 20px 0;
            text-align: justify; /* Justificado formal */
        }
        
        /* --- Lista de datos del equipo (como en la carta de vocho) --- */
        .data-list {
            margin-left: 30px;
            line-height: 1.8;
            font-size: 11pt;
        }
        .data-list strong {
            /* Asegura que todos los labels estén alineados */
            display: inline-block;
            width: 150px; 
            font-weight: bold;
            color: <?php echo $color_verde_udec; ?>;
        }
        
        /* --- El diagnóstico (con formato) --- */
        .diagnostico-block {
            margin-top: 15px;
            margin-left: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid <?php echo $color_verde_udec; ?>;
            font-family: 'courier', 'monospace'; /* Fuente monoespaciada para fallas */
            font-size: 10pt;
        }
        
        /* --- Cierre (Atentamente) --- */
        .closing {
            text-align: center;
            font-size: 11pt;
            /* * =============================================
             * CAMBIO CLAVE: ¡MÁXIMO ESPACIO PARA FIRMAS!
             * =============================================
             */
            margin-top: 130px; 
        }
        .signature-line {
            width: 280px;
            border-top: 1px solid <?php echo $color_texto; ?>;
            margin: 0 auto; /* Centrar la línea */
            padding-top: 8px; /* Espacio entre línea y texto */
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-title {
            font-size: 10pt;
        }

    </style>
</head>
<body>
    
    <div class="header-info">
        <strong>Delegación:</strong> <?php echo htmlspecialchars($dictamen['delegacion']); ?><br>
        <strong>Centro de Trabajo:</strong> <?php echo htmlspecialchars($dictamen['centro_trabajo']); ?><br>
        Colima, Col. a <?php echo $fecha_dictamen_formateada; ?>
    </div>

    <div class="recipient">
        A QUIEN CORRESPONDA<br>
        P R E S E N T E
    </div>

    <div class="subject">
        ASUNTO: DICTAMEN TÉCNICO DE EQUIPO DE CÓMPUTO<br>
        <span class="folio">FOLIO: <?php echo str_pad($dictamen['id'], 6, '0', STR_PAD_LEFT); ?></span>
    </div>

    <p class="main-text">
        Por medio del presente y a conveniencia del interesado(a) 
        <strong>C. <?php echo htmlspecialchars($dictamen['nombre_solicitante']); ?></strong>, 
        se emite el Dictamen Técnico del siguiente equipo de cómputo:
    </p>

    <div class="data-list">
        <strong>Tipo de Equipo:</strong> <?php echo htmlspecialchars($dictamen['tipo_equipo']); ?><br>
        <strong>Marca:</strong> <?php echo htmlspecialchars($dictamen['marca']); ?><br>
        <strong>Modelo:</strong> <?php echo htmlspecialchars($dictamen['modelo']); ?><br>
        <strong>Número de Serie:</strong> <?php echo htmlspecialchars($dictamen['numero_serie']); ?><br>
        <strong>Número de Inventario:</strong> <?php echo htmlspecialchars($dictamen['numero_inventario']); ?>
    </div>

    <p class="main-text">
        Con fecha <?php echo $fecha_dictamen_formateada; ?>, 
        se realizó la inspección, reportando el siguiente diagnóstico y observaciones:
    </p>

    <div class="diagnostico-block">
        <?php echo nl2br(htmlspecialchars($dictamen['diagnostico'])); ?>
    </div>
    
    <div class="closing">
        ATENTAMENTE<br>
        "SERVICIOS TELEMATICOS"
        
        <div class="signature-line" style="margin-top: 80px;">
            <span class="signature-name"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span><br>
            <span class="signature-title">Nombre del y firma del encargado: </span>
        </div>
    </div>

</body>
</html>