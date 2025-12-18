<?php
// includes/admin_check.php

// 1. Primero, nos aseguramos de que el usuario ESTÉ logueado.
// (Este script asume que session_start() ya fue llamado)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Segundo, verificamos que el usuario logueado sea 'admin'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Si no es admin, lo sacamos al dashboard principal.
    // (No le decimos que es una página de admin, solo que no tiene acceso)
    header('Location: dashboard.php');
    exit;
}

// Si el script llega hasta aquí, el usuario es un admin verificado.
?>