<?php
require 'includes/header.php';
require 'includes/admin_check.php';
require 'config/db.php';

$error = '';
$exito = '';

// --- Crear usuario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $delegacion = trim($_POST['delegacion']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($delegacion) || empty($nombre_completo) || empty($login_id) || empty($password) || empty($role)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (delegacion, nombre_completo, login_id, password_hash, role) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$delegacion, $nombre_completo, $login_id, $password_hash, $role]);
            $exito = "¡Usuario '$login_id' creado exitosamente!";
        } catch (PDOException $e) {
            $error = ($e->getCode() == 23000)
                ? "Error: El ID de Login '$login_id' ya existe."
                : "Error al crear usuario: " . $e->getMessage();
        }
    }
}

// --- Leer usuarios ---
try {
    $usuarios = $pdo->query("SELECT id, delegacion, nombre_completo, login_id, role, fecha_creacion 
                             FROM usuarios 
                             ORDER BY nombre_completo")->fetchAll();
} catch (PDOException $e) {
    $error_tabla = "Error al cargar la lista de usuarios: " . $e->getMessage();
    $usuarios = [];
}
?>

<h2>(Admin) Gestión de Usuarios</h2>
<hr>

<div class="container" style="max-width: 600px; margin-left: 0;">
    <h3>Crear Nuevo Usuario</h3>

    <?php if ($error): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
        <p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;">
            <?php echo $exito; ?>
        </p>
    <?php endif; ?>

    <form action="gestion_usuarios.php" method="POST">
        <input type="hidden" name="crear_usuario" value="1">

        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required>

        <label for="delegacion">Delegación (Ej: Colima, Manzanillo):</label>
        <input type="text" id="delegacion" name="delegacion" required>

        <label for="login_id">ID de Login (Ej: COLIMA_02):</label>
        <input type="text" id="login_id" name="login_id" required>

        <label for="password">Contraseña (mínimo 8 caracteres):</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Rol:</label>
        <select id="role" name="role" required>
            <option value="">Seleccione un rol</option>
            <option value="solicitante">Solicitante (Puede ver sus dictámenes)</option>
            <option value="tecnico">Técnico (Puede crear dictámenes)</option>
            <option value="admin">Administrador (Puede gestionar usuarios)</option>
        </select>

        <button type="submit">Crear Usuario</button>
    </form>
</div>

<hr style="margin-top: 2rem;">

<h3>Usuarios Registrados</h3>
<table id="tabla_general" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Delegación</th>
            <th>Login ID</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
            <td><?php echo htmlspecialchars($usuario['delegacion']); ?></td>
            <td><?php echo htmlspecialchars($usuario['login_id']); ?></td>
            <td>
                <?php 
                if ($usuario['role'] === 'admin') echo 'Administrador';
                elseif ($usuario['role'] === 'tecnico') echo 'Técnico';
                elseif ($usuario['role'] === 'solicitante') echo 'Solicitante';
                else echo ucfirst($usuario['role']);
                ?>
            </td>
            <td style="text-align: center;">
                <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" title="Editar Usuario">✏️</a>
                <?php if ($usuario['id'] !== $_SESSION['user_id']): ?>
                    <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>"
                       title="Eliminar Usuario"
                       onclick="return confirm('¿Seguro que deseas eliminar al usuario <?php echo htmlspecialchars($usuario['login_id']); ?>?');">🗑️</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>
