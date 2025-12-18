<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config/db.php'; 


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}


function log_sesion($pdo, $usuario_ingresado, $id_usuario, $es_exitoso) {
    
    $ip_direccion = $_SERVER['REMOTE_ADDR'] ?? null;
    $fecha_hora = date('Y-m-d H:i:s');

    $sql = "INSERT INTO logs_sesion (id_usuario, usuario_ingresado, fecha_hora, es_exitoso, ip_direccion) 
            VALUES (?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario, $usuario_ingresado, $fecha_hora, $es_exitoso, $ip_direccion]);
    } catch (PDOException $e) {
        
        error_log("Error al registrar log de sesión: " . $e->getMessage());
    }
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = $_POST['login_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login_id) || empty($password)) {
        $error = 'Por favor, ingrese ID y contraseña.';
    } else {
        try {
            
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE login_id = ?");
            $stmt->execute([$login_id]);
            $user = $stmt->fetch();

          
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // --- REGISTRO DE SESIÓN EXITOSO ---
                log_sesion($pdo, $login_id, $user['id'], true);
                // -----------------------------------

                session_regenerate_id(true); 
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_id'] = $user['login_id'];
                $_SESSION['delegacion'] = $user['delegacion'];
                $_SESSION['role'] = $user['role']; 
                $_SESSION['nombre_completo'] = $user['nombre_completo']; 
                
                header('Location: dashboard.php');
                exit;
            } else {
                
            
                $id_usuario_fallido = $user['id'] ?? null; 
                log_sesion($pdo, $login_id, $id_usuario_fallido, false);
                // -----------------------------------
                
                $error = 'ID de Login o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Dictámenes</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login de Mantenimiento</h2>
        
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="login_id">ID de Delegación:</label>
            <input type="text" id="login_id" name="login_id" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>