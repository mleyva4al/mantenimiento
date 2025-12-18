<?php
// test.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de Errores</h1>";

echo "<p>Paso 1: PHP funciona.</p>";

try {
    echo "<p>Paso 2: Intentando cargar 'config/db.php'...</p>";
    require 'config/db.php';
    echo "<p style='color:green;'>... 'config/db.php' cargado con éxito.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>... ERROR FATAL al cargar 'config/db.php': " . $e->getMessage() . "</p>";
}

try {
    echo "<p>Paso 3: Intentando conectar a la BD con PDO...</p>";
    if (isset($pdo)) {
        echo "<p style='color:green;'>... ¡Conexión a la base de datos '$db' exitosa!</p>";
    } else {
        echo "<p style='color:red;'>... ERROR: El archivo db.php se cargó pero no creó la variable \$pdo.</p>";
    }

} catch (PDOException $e) {
     echo "<p style='color:red;'>... ERROR DE PDO: No se pudo conectar. " . $e->getMessage() . "</p>";
}

echo "<hr><p>Prueba completada.</p>";

?>
