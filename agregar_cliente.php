<?php
require_once 'includes/header.php';
?>

<h2>Agregar Nuevo Cliente</h2>
<p>Ingresa los datos del nuevo cliente o solicitante.</p>

<form action="guardar_cliente.php" method="POST">
    <label>Nombre Completo:</label>
    <input type="text" name="nombre_completo" required>

    <label>Correo Electrónico:</label>
    <input type="text" name="correo" placeholder="ejemplo@correo.com" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" placeholder="3121234567">

    <label>Empresa o Institución:</label>
    <input type="text" name="empresa" placeholder="Opcional">

    <button type="submit">Guardar Cliente</button>
</form>

<a href="clientes.php" style="
    display: inline-block;
    margin-top: 1.2rem;
    color: var(--udec-verde);
    font-weight: 600;
    text-decoration: none;
">← Volver a la lista</a>

<?php require_once 'includes/footer.php'; ?>
