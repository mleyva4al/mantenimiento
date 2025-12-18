<?php


require 'config/db.php'; 


$id_marca = intval($_GET['id_marca'] ?? 0);

if ($id_marca <= 0) {
    
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}


try {
    $stmt = $pdo->prepare("SELECT nombre FROM modelos WHERE id_marca = ? ORDER BY nombre ASC");
    $stmt->execute([$id_marca]);
    
    $modelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
    header('Content-Type: application/json');
    echo json_encode($modelos);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>