<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$logged = isset($_SESSION['usuario_id']);

if (!$logged) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SESSION['perfil'] === 'FORNECEDOR') {
    header('Location: ../dashboards/index.php');
} else {
    header('Location: ../store/index.php');
}
exit;