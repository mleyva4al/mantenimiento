<?php


require 'includes/header.php';
require 'includes/admin_check.php'; 
require 'config/db.php';

$error = '';
$exito = '';
$user_id = $_GET['id'] ?? null;


if (!filter_var($user_id, FILTER_VALIDATE_INT)) {
    echo "<h1>Error</h1><p>ID de usuario no válido.</p>";
    require 'includes/footer.php';
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delegacion = trim($_POST['delegacion']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $login_id = trim($_POST['login_id']);
    $role = $_POST['role'];
    $password = $_POST['password']; 

    if (empty($delegacion) || empty($nombre_completo) || empty($login_id) || empty($role)) {
        $error = "Los campos (excepto contraseña) son obligatorios.";
    } else {
        try {
            
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $error = "La nueva contraseña debe tener al menos 8 caracteres.";
                } else {
                   
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET delegacion = ?, nombre_completo = ?, login_id = ?, role = ?, password_hash = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$delegacion, $nombre_completo, $login_id, $role, $password_hash, $user_id]);
                    $exito = "¡Usuario actualizado exitosamente (con nueva contraseña)!";
                }
            } else {
              
                $sql = "UPDATE usuarios SET delegacion = ?, nombre_completo = ?, login_id = ?, role = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$delegacion, $nombre_completo, $login_id, $role, $user_id]);
                $exito = "¡Usuario actualizado exitosamente (sin cambiar contraseña)!";
            }

        } catch (PDOException $e) {
            $error = ($e->getCode() == 23000) ? "Error: El ID de Login '$login_id' ya existe." : "Error al actualizar usuario: " . $e->getMessage();
        }
    }
}



try {
    $stmt = $pdo->prepare("SELECT delegacion, nombre_completo, login_id, role FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo "<h1>Error</h1><p>Usuario no encontrado.</p>";
        require 'includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    die("Error al cargar datos del usuario: " . $e->getMessage());
}

?>

<h2>Editar Usuario: <?php echo htmlspecialchars($usuario['nombre_completo']); ?></h2>
<hr>

<div class="container" style="max-width: 600px; margin-left: 0;">
    
    <?php if ($error): ?><p class="error-message"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($exito): ?><p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;"><?php echo $exito; ?></p><?php endif; ?>

    <form action="editar_usuario.php?id=<?php echo $user_id; ?>" method="POST">
        
        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required 
               value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>">

        <label for="delegacion">Delegación:</label>
        <input type="text" id="delegacion" name="delegacion" required 
               value="<?php echo htmlspecialchars($usuario['delegacion']); ?>">

        <label for="login_id">ID de Login:</label>
        <input type="text" id="login_id" name="login_id" required 
               value="<?php echo htmlspecialchars($usuario['login_id']); ?>">

        <label for="role">Rol:</label>
        <select id="role" name="role" required>
            <option value="tecnico" <?php echo ($usuario['role'] == 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
            <option value="admin" <?php echo ($usuario['role'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
        </select>
        
        <hr>
        <label for="password">Nueva Contraseña (Dejar en blanco para no cambiar):</label>
        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">

        <button type="submit">Actualizar Usuario</button>
        <a href="gestion_usuarios.php" style="display: block; text-align: center; margin-top: 1rem;">Cancelar y Volver</a>
    </form>
</div>

<?php
require 'includes/footer.php';
?>