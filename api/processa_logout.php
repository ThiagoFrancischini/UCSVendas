<?php
include_once(__DIR__ . '/../controllers/UsuarioController.php');

$controller = new UsuarioController();
$controller->logout();

header('Location: ../views/store/index.php');
exit;
?>