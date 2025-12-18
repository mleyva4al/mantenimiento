<?php

session_start();
require 'includes/auth_check.php'; 
require 'config/db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vista_general.php');
    exit;
}


$id_dictamen = $_POST['id_dictamen'] ?? null;
$nota = trim($_POST['nota'] ?? '');
$id_usuario = $_SESSION['user_id'] ?? null;


if (empty($id_dictamen) || !filter_var($id_dictamen, FILTER_VALIDATE_INT) || empty($nota) || empty($id_usuario)) {
   
    header("Location: editar_dictamen.php?id=" . urlencode($id_dictamen) . "&nota_error=Faltan datos esenciales.");
    exit;
}


try {
    $sql = "INSERT INTO dictamen_notas (id_dictamen, id_usuario, nota) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
  
    $stmt->execute([$id_dictamen, $id_usuario, $nota]);


    header("Location: editar_dictamen.php?id=" . urlencode($id_dictamen) . "&nota_exito=1");
    exit;

} catch (PDOException $e) {
    error_log("Error al guardar nota: " . $e->getMessage());
    header("Location: editar_dictamen.php?id=" . urlencode($id_dictamen) . "&nota_error=Error al guardar la nota en BD.");
    exit;
}

?>