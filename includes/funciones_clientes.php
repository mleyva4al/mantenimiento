<?php
require_once __DIR__ . '/../config/db.php';

function obtenerClientes() {
    global $pdo;
    $sql = "SELECT * FROM clientes ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function guardarCliente($nombre, $correo, $telefono, $empresa, $direccion = null) {
    global $pdo;
    $sql = "INSERT INTO clientes (nombre, correo, telefono, empresa, direccion, fecha_registro)
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $correo, $telefono, $empresa, $direccion]);
}
?>
