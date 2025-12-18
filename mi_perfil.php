<?php


require 'includes/header.php';     
require 'includes/auth_check.php'; 
require 'config/db.php';          

$exito_datos = '';
$error_datos = '';
$exito_pass = '';
$error_pass = '';

$user_id = $_SESSION['user_id']; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    if (isset($_POST['actualizar_datos'])) {
        $nombre_completo = trim($_POST['nombre_completo']);
        
        if (empty($nombre_completo)) {
            $error_datos = "El nombre completo no puede estar vacío.";
        } else {
            try {
                $sql = "UPDATE usuarios SET nombre_completo = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre_completo, $user_id]);
                
                
                $_SESSION['nombre_completo'] = $nombre_completo;
                
                $exito_datos = "¡Nombre completo actualizado exitosamente!";
            } catch (PDOException $e) {
                $error_datos = "Error al actualizar el nombre: " . $e->getMessage();
            }
        }
    }

   
    if (isset($_POST['actualizar_pass'])) {
        $pass_actual = $_POST['pass_actual'];
        $pass_nuevo = $_POST['pass_nuevo'];
        $pass_confirmar = $_POST['pass_confirmar'];

        // 1. Validaciones simples
        if (empty($pass_actual) || empty($pass_nuevo) || empty($pass_confirmar)) {
            $error_pass = "Todos los campos de contraseña son obligatorios.";
        } elseif ($pass_nuevo !== $pass_confirmar) {
            $error_pass = "Las contraseñas nuevas no coinciden.";
        } elseif (strlen($pass_nuevo) < 8) {
            $error_pass = "La contraseña nueva debe tener al menos 8 caracteres.";
        } else {
            // 2. Validación de seguridad (verificar la contraseña actual)
            try {
                
                $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($pass_actual, $user['password_hash'])) {
                    
                    // 3. Hashear la nueva contraseña
                    $nuevo_hash = password_hash($pass_nuevo, PASSWORD_DEFAULT);
                    
                    
                    $sql_update = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
                    $stmt_update = $pdo->prepare($sql_update);
                    $stmt_update->execute([$nuevo_hash, $user_id]);
                    
                    $exito_pass = "¡Contraseña actualizada exitosamente!";
                    
                } else {
                   
                    $error_pass = "La 'Contraseña Actual' que ingresaste es incorrecta.";
                }
            } catch (PDOException $e) {
                $error_pass = "Error al verificar la contraseña: " . $e->getMessage();
            }
        }
    }
}
?>

<h2>Mi Perfil</h2>
<hr>


<div style="display: flex; flex-wrap: wrap; gap: 2rem;">

    <!-- Columna 1: Datos Personales -->
    <div style="flex: 1; min-width: 300px;">
        <h3>Datos Personales</h3>
        
        <div class="container" style="padding: 1.5rem; margin: 0;">
            <?php if ($error_datos): ?><p class="error-message"><?php echo $error_datos; ?></p><?php endif; ?>
            <?php if ($exito_datos): ?><p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;"><?php echo $exito_datos; ?></p><?php endif; ?>

            <form action="mi_perfil.php" method="POST">
                <input type="hidden" name="actualizar_datos" value="1">
                
                <label for="nombre_completo">Nombre Completo:</label>
                
                <input type="text" id="nombre_completo" name="nombre_completo" 
                       value="<?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>" required>
                
                
                <label>ID de Login:</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['login_id']); ?>" disabled>
                
                <label>Delegación:</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['delegacion']); ?>" disabled>
                
                <label>Rol:</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['role']); ?>" disabled>

                <button type="submit">Actualizar Nombre</button>
            </form>
        </div>
    </div>

    <!-- Columna 2: Cambiar Contraseña -->
    <div style="flex: 1; min-width: 300px;">
        <h3>Cambiar Contraseña</h3>
        
        <div class="container" style="padding: 1.5rem; margin: 0;">
            <?php if ($error_pass): ?><p class="error-message"><?php echo $error_pass; ?></p><?php endif; ?>
            <?php if ($exito_pass): ?><p style="color: green; background-color: #e6ffed; border: 1px solid green; padding: 10px; border-radius: 8px;"><?php echo $exito_pass; ?></p><?php endif; ?>

            <form action="mi_perfil.php" method="POST">
                <input type="hidden" name="actualizar_pass" value="1">
                
                <label for="pass_actual">Contraseña Actual:</label>
                <input type="password" id="pass_actual" name="pass_actual" required>
                
                <label for="pass_nuevo">Contraseña Nueva:</label>
                <input type="password" id="pass_nuevo" name="pass_nuevo" required>
                
                <label for="pass_confirmar">Confirmar Contraseña Nueva:</label>
                <input type="password" id="pass_confirmar" name="pass_confirmar" required>

                <button type="submit">Cambiar Contraseña</button>
            </form>
        </div>
    </div>

</div>

<?php
require 'includes/footer.php'; 
?>