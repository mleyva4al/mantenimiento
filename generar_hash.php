<?php

echo "Hash para Colima (ColimaPass123):<br>";
echo password_hash('ColimaPass123', PASSWORD_DEFAULT);
echo "<hr>";

echo "Hash para Manzanillo (ManzanilloPass123):<br>";
echo password_hash('ManzanilloPass123', PASSWORD_DEFAULT);
echo "<hr>";

echo "Hash para Tecoman (TecomanPass123):<br>";
echo password_hash('TecomanPass123', PASSWORD_DEFAULT);
echo "<hr>";
?>