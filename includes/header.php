<?php
// --------------------------------------------
// CONFIGURACIÓN GLOBAL
// --------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir funciones globales
$funciones_clientes = __DIR__ . '/funciones_clientes.php';
if (file_exists($funciones_clientes)) {
    require_once $funciones_clientes;
} else {
    echo "<div style='background:#fcc;color:#900;padding:1rem;border:1px solid #c00'>
            ⚠️ Error: No se encontró el archivo <b>includes/funciones_clientes.php</b>
          </div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Dictámenes - UdeC</title>

    <link rel="stylesheet" href="public/css/style.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        /* 🔹 Ajustes específicos del navbar (respetando tu style.css) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            border-bottom: 3px solid var(--udec-verde);
            box-shadow: var(--shadow);
            padding: 1rem 2rem;
            flex-wrap: wrap; /* para móviles */
        }

        .navbar .nav-logo a {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--udec-verde);
            text-decoration: none;
        }

        .navbar .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .navbar .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.5rem 0.8rem;
            border-radius: var(--radius);
            transition: background-color 0.2s ease;
        }

        .navbar .nav-links a:hover {
            background-color: var(--light-gray);
        }

        .navbar .usuario {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--udec-verde);
            font-weight: 600;
        }

        .navbar .usuario span {
            font-size: 0.95rem;
        }

        .navbar .logout {
            background-color: var(--udec-verde);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }

        .navbar .logout:hover {
            background-color: var(--udec-verde-oscuro);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .navbar .nav-links {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="nav-logo">
        <a href="dashboard.php">Sistema de Dictámenes</a>
    </div>

    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Nuevo Dictamen</a>
            <a href="vista_general.php">Vista General</a>
            <a href="clientes.php">Clientes</a>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="gestion_usuarios.php" style="color: var(--udec-oro); font-weight: bold;">(Admin) Usuarios</a>
                <a href="gestion_inventario.php" style="color: var(--udec-oro); font-weight: bold;">(Admin) Inventario</a>
                <a href="reporte_sesiones.php" style="color: var(--udec-oro); font-weight: bold;">(Admin) Sesiones</a>
                <a href="reporte_acciones.php" style="color: var(--udec-oro); font-weight: bold;">(Admin) Bitácora</a>
            <?php endif; ?>

            <div class="usuario">
                <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span>
                <a href="logout.php" class="logout">Salir</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
<!-- 🔸 El contenido de la página comienza aquí -->
