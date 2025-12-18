<?php

require_once('vendor/autoload.php'); 
session_start();
require 'includes/auth_check.php'; 
require 'config/db.php';

$id_dictamen = $_GET['id'] ?? null;

if (!filter_var($id_dictamen, FILTER_VALIDATE_INT)) {
    die("ID no válido.");
}

try {
    
    $stmt = $pdo->prepare("SELECT * FROM dictamenes WHERE id = ?");
    $stmt->execute([$id_dictamen]);
    $dictamen = $stmt->fetch();

    if (!$dictamen) {
        die("Dictamen no encontrado.");
    }


    if ($_SESSION['role'] === 'tecnico' && $dictamen['delegacion'] !== $_SESSION['delegacion']) {
       
        die("Acceso Denegado. No tienes permiso para ver este dictamen.");
    }
   

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Mantenimiento UdeC');
$pdf->SetTitle('Dictamen Folio ' . $id_dictamen);
$pdf->SetSubject('Dictamen Técnico');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);


ob_start(); 
include 'templates/pdf_template.php'; 
$html = ob_get_clean(); 

// Escribir el HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y enviar el PDF
$pdf->Output('dictamen_folio_' . $id_dictamen . '.pdf', 'I'); // 'I' para mostrar en navegador

?>