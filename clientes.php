<?php
require_once 'includes/header.php';
require_once 'includes/funciones_clientes.php';
$clientes = obtenerClientes();
?>

<h2>Gestión de Clientes</h2>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <p>Administra la información de tus clientes o solicitantes para los dictámenes.</p>
    <a href="agregar_cliente.php" style="
        background-color: var(--udec-verde);
        color: white;
        padding: 0.6rem 1rem;
        border-radius: var(--radius);
        text-decoration: none;
        font-weight: 600;
    ">+ Nuevo Cliente</a>
</div>

<table id="tabla_clientes" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Empresa</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['correo']) ?></td>
            <td><?= htmlspecialchars($c['telefono']) ?></td>
            <td><?= htmlspecialchars($c['empresa']) ?></td>
            <td><?= htmlspecialchars($c['direccion']) ?></td>
            <td>
                <a href="editar_cliente.php?id=<?= $c['id'] ?>">✏️ Editar</a> |
                <a href="eliminar_cliente.php?id=<?= $c['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">🗑️ Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#tabla_clientes').DataTable();
});
</script>

<?php require_once 'includes/footer.php'; ?>
