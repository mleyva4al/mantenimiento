<?php
$host = "localhost";
$user = "USUARIO_AQUI";
$password = "PASSWORD_AQUI";
$database = "NOMBRE_BD_AQUI";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión");
}

