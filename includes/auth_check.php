<?php
// includes/auth_check.php

// ESTE SCRIPT ASUME QUE session_start() YA FUE LLAMADO

if (!isset($_SESSION['user_id'])) {
    // Si no hay sesión, lo mandamos al login
    header('Location: login.php');
    exit;
}
?>